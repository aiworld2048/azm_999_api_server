<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserType;
use App\Models\Admin\Bank;
use App\Models\Admin\Banner;
use App\Models\Admin\BannerAds;
use App\Models\Admin\BannerText;
use App\Models\Admin\Permission;
use App\Models\Admin\Promotion;
use App\Models\Admin\Role;
use App\Models\PlaceBet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    private const PLAYER_ROLE = 3;

    private const AGENT_ROLE = 2;

    private const OWNER_ROLE = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
   protected $fillable = [
        'user_name',
        'name',
        'profile',
        'email',
        'password',
        'game_provider_password',
        'profile',
        'phone',
        'balance',
        'max_score',
        'agent_id',
        'status',
        'type',
        'is_changed_password',
        'agent_logo',
        'site_name',
        'site_link',
        'shan_agent_code',
        'shan_agent_name',
        'shan_secret_key',
        'shan_callback_url',
        'client_agent_name',
        'client_agent_id',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    public function hasRole($role)
    {
        return $this->roles->contains('title', $role);
    }

    // A user can have children (e.g., Admin has many Agents, or Agent has many Players)
    public function children()
    {
        return $this->hasMany(User::class, 'agent_id', 'id');
    }

    // A user belongs to an agent (parent)
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // Fetch players managed by an agent
    public function players()
    {
        return $this->hasMany(User::class, 'agent_id');
    }

    

    // A user can have a parent (e.g., Agent belongs to an Admin)
    public function parent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // Get all players under an agent
    public function Agentplayers()
    {
        return $this->children()->whereHas('roles', function ($query) {
            $query->where('role_id', self::PLAYER_ROLE);
        });
    }

    

    /**
     * Recursive relationship to get all ancestors up to senior.
     */
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    /**
     * Recursive relationship to get all descendants down to players.
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    public function agents()
    {
        return $this->hasMany(User::class, 'agent_id');
    }

    
    public static function adminUser()
    {
        return self::where('type', UserType::OWNER)->first();
    }

    /**
     * Get the game provider password for this user.
     */
    public function getGameProviderPassword(): ?string
    {
        if ($this->game_provider_password) {
            try {
                return Crypt::decryptString($this->game_provider_password);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // Log the error or handle it as appropriate (e.g., return null to regenerate)
                \Log::error('Failed to decrypt game_provider_password for user '.$this->id, ['error' => $e->getMessage()]);

                return null;
            }
        }

        return null;
    }

    /**
     * Set the game provider password for this user.
     */
    public function setGameProviderPassword(string $password): void
    {
        $this->game_provider_password = Crypt::encryptString($password);
        $this->save(); // Save the user model to persist the password
    }

    

    public function hasPermission($permission)
    {
        // Owner has all permissions
        if ($this->hasRole('Owner')) {
            return true;
        }

       

        // Agent has all permissions
        if ($this->hasRole('Agent')) {
            return true;
        }

        

        // Default: deny permission
        return false;
    }

    

    public function getAllDescendantPlayers()
    {
        // Fetch direct players
        $players = $this->children()->where('type', \App\Enums\UserType::Player)->get();

        // Fetch all subagents
        $subagents = $this->children()->where('type', \App\Enums\UserType::SubAgent)->get();

        // For each subagent, fetch their direct players recursively
        foreach ($subagents as $sub) {
            $players = $players->merge($sub->getAllDescendantPlayers());
        }

        return $players;
    }

    

    public function reportTransactionsAsPlayer()
    {
        return $this->hasMany(ReportTransaction::class, 'user_id');
    }

    public function poneWinePlayer()
    {
        return $this->hasMany(PlaceBet::class, 'player_id', 'id');
    }
}
