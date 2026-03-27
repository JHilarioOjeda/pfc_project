<?php

namespace App\Livewire\StartChecklist;

use App\Models\StartChecklist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Modal extends Component
{
    public $show = false;

    public $answers = [];

    public $reasons = [];

    public $questions = [];

    public function mount(): void
    {
        $this->questions = (array) config('start_checklist.questions', []);
        foreach ($this->questions as $question) {
            $key = $question['key'] ?? null;
            if ($key && !array_key_exists($key, $this->answers)) {
                $this->answers[$key] = null;
                $this->reasons[$key] = null;
            }
        }

        $this->updateVisibility();
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

    public function updateVisibility(): void
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

            $required = (bool) ($question['required'] ?? true);
            $rules["answers.$key"] = $required ? 'required|in:0,1' : 'nullable|in:0,1';

            $isNo = isset($this->answers[$key]) && (string) $this->answers[$key] === '0';
            $rules["reasons.$key"] = $isNo ? 'required|string|max:500' : 'nullable|string|max:500';
        }

        return $rules;
    }

    protected function messages(): array
    {
        $messages = [];
        foreach ($this->questions as $question) {
            $key = $question['key'] ?? null;
            if (!$key) {
                continue;
            }
            $messages["reasons.$key.required"] = 'La razón es obligatoria cuando la respuesta es No.';
        }
        return $messages;
    }

    public function save(): void
    {
        $this->validate();

        $user = Auth::user();
        if (!$user) {
            return;
        }

        DB::beginTransaction();

        try {
            if ($this->hasChecklistForToday()) {
                DB::commit();
                $this->show = false;
                return;
            }

            $questions = [];
            foreach ($this->questions as $question) {
                $key = $question['key'] ?? null;
                if (!$key) {
                    continue;
                }

                $answer = $this->answers[$key] ?? null;
                if ($answer !== null) {
                    $answer = (bool) ((int) $answer);
                }

                $questions[] = [
                    'key' => $key,
                    'label' => $question['label'] ?? $key,
                    'type' => $question['type'] ?? 'boolean',
                    'answer' => $answer,
                    'reason' => ($answer === false) ? ($this->reasons[$key] ?? null) : null,
                ];
            }

            StartChecklist::create([
                'id_user' => $user->id,
                'questions' => $questions,
                'register_date' => now(),
            ]);

            DB::commit();

            $this->show = false;
            LivewireAlert::title('Checklist de arranque guardado con éxito.')
                ->success()
                ->show();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('save', 'No se pudo guardar el checklist de arranque. Intenta de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.start-checklist.modal');
    }
}
