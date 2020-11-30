<?php

namespace Notifications\DataTransferObjects\Models;

use App\Support\Helpers\ArrExtended;
use Data\DataTransferObjects\Traits\Translatable;
use Data\DataTransferObjects\Translations\TranslationDTO;
use Illuminate\Contracts\Support\Arrayable;

class EmailTemplateDTO implements Arrayable
{
    use Translatable;
    public string $slug;
    public TranslationDTO $label;
    public string $image;
    public TranslationDTO $emailSubject;
    public string $emailBody;

    public function __construct(
        string $slug,
        TranslationDTO $label,
        string $image,
        TranslationDTO $emailSubject,
        string $emailBody
    ) {
        $this->slug = $slug;
        $this->label = $label;
        $this->image = $image;
        $this->emailSubject = $emailSubject;
        $this->emailBody = $emailBody;
    }

    public function toArray(): array
    {
        return ArrExtended::filter_nulls([
            'slug' => $this->slug,
            'label' => $this->label->getText(),
            'image' => $this->image,
            'email_subject' => $this->emailSubject->getText(),
            'email_body' => $this->emailBody,
        ]);
    }
}
