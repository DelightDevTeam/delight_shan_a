<?php

namespace App\Http\Requests;

use App\Models\User;
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
            $this->member = User::where('user_name', $this->getMemberName())->first();
        }

        return $this->member;
    }

    public function getMemberName()
    {
        return $this->get('PlayerId');
    }
}
