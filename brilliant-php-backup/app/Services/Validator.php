<?php
declare(strict_types=1);

namespace App\Services;

class Validator
{
    private array $errors = [];

    public function rule(string $field, mixed $value, string $rules, array $data = []): void
    {
        foreach (explode('|', $rules) as $rule) {
            $this->apply($field, $value, $rule, $data);
        }
    }

    private function apply(string $field, mixed $value, string $rule, array $data): void
    {
        $ruleName = $rule;
        $param = null;
        if (str_contains($rule, ':')) {
            [$ruleName, $param] = explode(':', $rule, 2);
        }
        $value = is_scalar($value) ? trim((string)$value) : $value;

        switch ($ruleName) {
            case 'required':
                if ($value === '' || $value === null || $value === false) {
                    $this->errors[$field][] = $this->label($field) . ' is required.';
                }
                break;
            case 'email':
                if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = $this->label($field) . ' must be a valid email.';
                }
                break;
            case 'numeric':
                if ($value !== '' && !is_numeric($value)) {
                    $this->errors[$field][] = $this->label($field) . ' must be a number.';
                }
                break;
            case 'min':
                if ($value !== '' && is_numeric($value) && (float)$value < (float)$param) {
                    $this->errors[$field][] = $this->label($field) . ' must be at least ' . $param . '.';
                }
                break;
        }
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function errorsFlat(): array
    {
        $out = [];
        foreach ($this->errors as $field => $msgs) {
            foreach ($msgs as $m) $out[] = $m;
        }
        return $out;
    }

    private function label(string $field): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $field));
    }
}
