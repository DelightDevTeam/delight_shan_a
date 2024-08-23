<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;

class SlotWebhookRequest extends FormRequest
{
    private ?User $member;
   
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    public function getMember()
    {
        if (! isset($this->member)) {
            $playerId = $this->getMemberName();
            Log::info('Searching for user with PlayerId:', ['PlayerId' => $playerId]);
            $this->member = User::where('user_name', $playerId)->first();

            if (!$this->member) {
                Log::warning('No user found with PlayerId:', ['PlayerId' => $playerId]);
            } else {
                Log::info('User found:', ['UserId' => $this->member->id]);
            }
        }

        return $this->member;
    }

    public function getMemberName()
    {
        return $this->get('PlayerId');
    }
}
