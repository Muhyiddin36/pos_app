<?php
/**
 * Validator input berbasis aturan string sederhana, mis:
 * ['name' => 'required|max:100', 'email' => 'email', 'price' => 'required|numeric']
 */

declare(strict_types=1);

final class Validator
{
    private array $errors = [];

    /**
     * @param array<string,mixed> $data
     * @param array<string,string> $rules
     */
    public function __construct(private array $data, private array $rules)
    {
    }

    public static function make(array $data, array $rules): self
    {
        $validator = new self($data, $rules);
        $validator->run();
        return $validator;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function run(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            foreach (explode('|', $ruleString) as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $this->applyRule($field, $value, $name, $param);
            }
        }
    }

    private function applyRule(string $field, mixed $value, string $rule, ?string $param): void
    {
        if (isset($this->errors[$field])) {
            return; // satu pesan error per field sudah cukup
        }

        switch ($rule) {
            case 'required':
                if ($value === null || $value === '') {
                    $this->errors[$field] = 'Kolom ' . $field . ' wajib diisi.';
                }
                break;
            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->errors[$field] = 'Kolom ' . $field . ' harus berupa angka.';
                }
                break;
            case 'integer':
                if ($value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->errors[$field] = 'Kolom ' . $field . ' harus berupa bilangan bulat.';
                }
                break;
            case 'email':
                if ($value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                    $this->errors[$field] = 'Format email tidak valid.';
                }
                break;
            case 'min':
                if ($value !== null && mb_strlen((string) $value) < (int) $param) {
                    $this->errors[$field] = 'Kolom ' . $field . ' minimal ' . $param . ' karakter.';
                }
                break;
            case 'max':
                if ($value !== null && mb_strlen((string) $value) > (int) $param) {
                    $this->errors[$field] = 'Kolom ' . $field . ' maksimal ' . $param . ' karakter.';
                }
                break;
            case 'min_value':
                if ($value !== null && $value !== '' && (float) $value < (float) $param) {
                    $this->errors[$field] = 'Kolom ' . $field . ' minimal ' . $param . '.';
                }
                break;
            case 'in':
                $allowed = explode(',', (string) $param);
                if ($value !== null && $value !== '' && !in_array((string) $value, $allowed, true)) {
                    $this->errors[$field] = 'Nilai ' . $field . ' tidak valid.';
                }
                break;
        }
    }
}
