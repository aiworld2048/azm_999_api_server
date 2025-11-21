<?php

namespace Database\Seeders;

use App\Services\GameListService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

abstract class BaseGameListSeeder extends Seeder
{
    /**
     * Seed game list entries from a provider JSON dump.
     *
     * @param  string  $jsonFilename  File located under app/Console/Commands/data
     * @param  array<string, mixed>  $options
     */
    protected function seedFromJson(string $jsonFilename, array $options = []): void
    {
        $jsonPath = base_path(($options['directory'] ?? 'app/Console/Commands/data/').$jsonFilename);

        if (! File::exists($jsonPath)) {
            $this->command?->warn("Game list json file not found: {$jsonFilename}");

            return;
        }

        $payload = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command?->error("Invalid JSON in {$jsonFilename}: ".json_last_error_msg());

            return;
        }

        $games = $payload['provider_games'] ?? $payload['ProviderGames'] ?? null;

        if (! is_array($games) || $games === []) {
            $this->command?->warn("No provider_games found in {$jsonFilename}");

            return;
        }

        $this->seedGames($games, array_merge($options, [
            'source' => $jsonFilename,
        ]));
    }

    /**
     * Seed game list entries by calling the remote game list API.
     *
     * Supported options:
     * - product_code (required)
     * - operator_code (defaults to config('seamless_key.agent_code'))
     * - game_type
     * - offset
     * - size
     * - status_key / status_active_value / provider / game_list_status (same as seedFromJson)
     *
     * @param  array<string, mixed>  $options
     */
    protected function seedFromApi(array $options): void
    {
        $productCode = $options['product_code'] ?? null;
        $operatorCode = $options['operator_code'] ?? config('seamless_key.agent_code');
        $gameType = $options['game_type'] ?? null;
        $offset = $options['offset'] ?? 0;
        $size = $options['size'] ?? null;

        if (! $productCode) {
            $this->command?->warn('seedFromApi requires a product_code option.');

            return;
        }

        if (! $operatorCode) {
            $this->command?->warn("Operator code not configured for product_code {$productCode}.");

            return;
        }

        try {
            $response = GameListService::getGameList((int) $productCode, (string) $operatorCode, $gameType, $offset, $size);
        } catch (\Throwable $exception) {
            $this->command?->error("Failed to fetch game list for product_code {$productCode}: {$exception->getMessage()}");

            return;
        }

        $games = $response['provider_games'] ?? $response['data']['provider_games'] ?? null;

        if (! is_array($games) || $games === []) {
            $this->command?->warn("Game list API returned no games for product_code {$productCode}.");

            return;
        }

        $this->seedGames($games, array_merge($options, [
            'source' => 'api:'.$productCode,
        ]));
    }

    /**
     * Persist the processed games into the database.
     *
     * @param  array<int, array<string, mixed>>  $games
     * @param  array<string, mixed>  $options
     */
    protected function seedGames(array $games, array $options = []): void
    {
        $productCode = $options['product_code'] ?? null;

        if (! $productCode) {
            foreach ($games as $game) {
                if (! empty($game['product_code'])) {
                    $productCode = $game['product_code'];
                    break;
                }
            }
        }

        if (! $productCode) {
            $this->command?->warn('Unable to determine product_code for game list seeding.');

            return;
        }

        $product = DB::table('products')
            ->where('product_code', $productCode)
            ->first();

        if (! $product) {
            $this->command?->warn("Product not found for code {$productCode} (source: {$options['source'] ?? 'unknown'})");

            return;
        }

        $gameTypeCode = $options['game_type'] ?? $product->game_type;

        $gameTypeId = DB::table('game_types')
            ->where('code', $gameTypeCode)
            ->value('id');

        if (! $gameTypeId) {
            $this->command?->warn("Game type {$gameTypeCode} not found for product {$product->product_name}");

            return;
        }

        $statusKey = $options['status_key'] ?? 'status';
        $activeValue = $options['status_active_value'] ?? 'ACTIVATED';
        $gameListStatus = $options['game_list_status'] ?? ($product->game_list_status ?? 1);
        $provider = $options['provider'] ?? $product->provider;
        $now = now();
        $rows = [];

        foreach ($games as $game) {
            $status = $game[$statusKey] ?? null;

            if ($status !== $activeValue) {
                continue;
            }

            $row = [
                'game_code' => $game['game_code'] ?? null,
                'game_name' => $game['game_name'] ?? null,
                'game_type' => $game['game_type'] ?? $gameTypeCode,
                'image_url' => $game['image_url'] ?? null,
                'provider_product_id' => $game['product_id'] ?? null,
                'game_type_id' => $gameTypeId,
                'product_id' => $product->id,
                'product_code' => $game['product_code'] ?? $productCode,
                'support_currency' => $game['support_currency'] ?? null,
                'status' => $status,
                'provider' => $provider,
                'game_list_status' => $gameListStatus,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (isset($options['transform']) && is_callable($options['transform'])) {
                $row = $options['transform']($row, $game, $product);

                if (! $row) {
                    continue;
                }
            }

            $rows[] = $row;
        }

        if ($rows === []) {
            $this->command?->warn("No active games to seed for product_code {$productCode}.");

            return;
        }

        DB::table('game_lists')
            ->where('product_id', $product->id)
            ->delete();

        DB::table('game_lists')->insert($rows);

        $this->command?->info("Seeded ".count($rows)." games for product_code {$productCode}.");
    }
}

