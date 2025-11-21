<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ThreeDWinnerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $betData;
    protected $winNumber;
    protected $drawSession;
    protected $prizeAmount;
    protected $isExactWinner;
    protected $isPermutationWinner;
    protected $matchingPermutations;

    public function __construct($betData, $winNumber, $drawSession, $prizeAmount, $isExactWinner, $isPermutationWinner, $matchingPermutations = [])
    {
        $this->betData = $betData;
        $this->winNumber = $winNumber;
        $this->drawSession = $drawSession;
        $this->prizeAmount = $prizeAmount;
        $this->isExactWinner = $isExactWinner;
        $this->isPermutationWinner = $isPermutationWinner;
        $this->matchingPermutations = $matchingPermutations;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        $message = $this->generateMessage();
        
        Log::info('Storing 3D winner notification in database:', [
            'player_name' => $notifiable->user_name,
            'bet_number' => $this->betData['bet_number'],
            'win_number' => $this->winNumber,
            'prize_amount' => $this->prizeAmount,
            'message' => $message,
        ]);

        return [
            'player_name' => $notifiable->user_name,
            'bet_number' => $this->betData['bet_number'],
            'win_number' => $this->winNumber,
            'draw_session' => $this->drawSession,
            'prize_amount' => $this->prizeAmount,
            'is_exact_winner' => $this->isExactWinner,
            'is_permutation_winner' => $this->isPermutationWinner,
            'matching_permutations' => $this->matchingPermutations,
            'message' => $message,
            'type' => 'three_d_winner',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'player_name' => $notifiable->user_name,
            'bet_number' => $this->betData['bet_number'],
            'win_number' => $this->winNumber,
            'draw_session' => $this->drawSession,
            'prize_amount' => $this->prizeAmount,
            'is_exact_winner' => $this->isExactWinner,
            'is_permutation_winner' => $this->isPermutationWinner,
            'matching_permutations' => $this->matchingPermutations,
            'message' => $this->generateMessage(),
            'type' => 'three_d_winner',
        ]);
    }

    private function generateMessage()
    {
        if ($this->isExactWinner) {
            return "🎉 CONGRATULATIONS! You won the FIRST PRIZE! Your bet {$this->betData['bet_number']} exactly matched the winning number {$this->winNumber} for draw session {$this->drawSession}. Prize: {$this->prizeAmount}";
        } elseif ($this->isPermutationWinner) {
            $permutationText = implode(', ', $this->matchingPermutations);
            return "🎯 PERMUTATION WIN! Your bet {$this->betData['bet_number']} matched the winning number {$this->winNumber} through permutations: {$permutationText} for draw session {$this->drawSession}. Prize: {$this->prizeAmount}";
        }
        
        return "You won {$this->prizeAmount} for your 3D bet!";
    }
}
