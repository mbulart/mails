<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'exists:mail_types,slug'],
            'to' => ['required', 'array', 'min:1'],
            'to.*' => ['required', 'email'],
            'cc' => ['sometimes', 'array'],
            'cc.*' => ['email'],
            'bcc' => ['sometimes', 'array'],
            'bcc.*' => ['email'],
            'variables' => ['sometimes', 'array'],
            'attachments' => ['sometimes', 'array', 'max:10'],
            'attachments.*.type' => ['required_with:attachments', 'in:local,url,base64'],
            'attachments.*.name' => ['sometimes', 'string', 'max:255'],
            'attachments.*.mime' => ['sometimes', 'string', 'max:120'],
            'attachments.*.path' => ['required_if:attachments.*.type,local', 'string'],
            'attachments.*.url' => ['required_if:attachments.*.type,url', 'url'],
            'attachments.*.content' => ['required_if:attachments.*.type,base64', 'string'],
        ];
    }
}
