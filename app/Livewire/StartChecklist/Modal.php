<?php

namespace App\Livewire\StartChecklist;

use App\Models\StartChecklist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;

class Modal extends Component
{
    public bool $show = false;

    /** @var array<string, mixed> */
    public array $answers = [];

    /** @var array<int, array<string, mixed>> */
    public array $questions = [];

    public function mount(): void
    {
        $this->questions = (array) config('start_checklist.questions', []);
        $this->initializeAnswers();
        $this->refreshVisibility();
    }

    protected function initializeAnswers(): void
    {
        foreach ($this->questions as $question) {
            $key = $question['key'] ?? null;
            if (!$key) {
                continue;
            }

            if (!array_key_exists($key, $this->answers)) {
                $this->answers[$key] = null;
            }
        }
    }

    protected function isLeaderProductionUser(): bool
    {
        $user = Auth::user();

        return $user
            && isset($user->user_type)
            && (int) $user->user_type === 3;
    }

    protected function hasChecklistForToday(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return true;
        }

        $today = now()->toDateString();

        return StartChecklist::query()
            ->where('id_user', $user->id)
            ->where(function ($query) use ($today) {
                $query->whereDate('register_date', $today)
                    ->orWhere(function ($q) use ($today) {
                        $q->whereNull('register_date')
                            ->whereDate('created_at', $today);
                    });
            })
            ->exists();
    }

    public function refreshVisibility(): void
    {
        if (!$this->isLeaderProductionUser()) {
            $this->show = false;
            return;
        }

        $this->show = !$this->hasChecklistForToday();
    }

    protected function rules(): array
    {
        $rules = [];

        foreach ($this->questions as $question) {
            $key = $question['key'] ?? null;
            if (!$key) {
                continue;
            }

            $type = $question['type'] ?? 'boolean';
            $required = (bool) ($question['required'] ?? true);

            if ($type === 'boolean') {
                $rules["answers.$key"] = ($required ? 'required|' : 'nullable|') . 'in:0,1';
                continue;
            }

            if ($type === 'text') {
                $max = (int) ($question['max'] ?? 255);
                $rules["answers.$key"] = ($required ? 'required|' : 'nullable|') . "string|max:$max";
                continue;
            }

            if ($type === 'number') {
                $rules["answers.$key"] = ($required ? 'required|' : 'nullable|') . 'numeric';
                continue;
            }

            $rules["answers.$key"] = ($required ? 'required|' : 'nullable|') . 'string';
        }

        return $rules;
    }

    protected function validationAttributes(): array
    {
        $attributes = [];

        foreach ($this->questions as $question) {
            $key = $question['key'] ?? null;
            if (!$key) {
                continue;
            }

            $attributes["answers.$key"] = $question['label'] ?? $key;
        }

        return $attributes;
    }

    protected function buildQuestionsPayload(): array
    {
        $payload = [];

        foreach ($this->questions as $question) {
            $key = $question['key'] ?? null;
            if (!$key) {
                continue;
            }

            $type = $question['type'] ?? 'boolean';
            $answer = $this->answers[$key] ?? null;

            if ($type === 'boolean' && $answer !== null) {
                $answer = (bool) ((int) $answer);
            }

            $payload[] = [
                'key' => $key,
                'label' => $question['label'] ?? $key,
                'type' => $type,
                'answer' => $answer,
            ];
        }

        return $payload;
    }

    public function save(): void
    {
        $this->refreshVisibility();
        if (!$this->show) {
            return;
        }

        $this->validate();

        $user = Auth::user();
        if (!$user) {
            return;
        }

        DB::beginTransaction();

        try {
            if (!$this->hasChecklistForToday()) {
                StartChecklist::create([
                    'id_user' => $user->id,
                    'questions' => $this->buildQuestionsPayload(),
                    'register_date' => now(),
                ]);
            }

            DB::commit();

            $this->show = false;
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Error al guardar start checklist: ' . $e->getMessage());
            $this->addError('save', 'No se pudo guardar el checklist. Intenta de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.start-checklist.modal');
    }
}
