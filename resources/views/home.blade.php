@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h1>Home</h1>

    <style>
        .prompt-panel {
            margin-top: 20px;
            max-width: 760px;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px;
            background: #ffffff;
        }

        .prompt-label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text);
        }

        .prompt-input {
            width: 100%;
            min-height: 140px;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px;
            font-size: 0.95rem;
            font-family: inherit;
            color: var(--text);
            resize: vertical;
        }

        .prompt-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.14);
        }

        .prompt-submit {
            margin-top: 12px;
            border: 0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #ffffff;
            background: var(--primary);
            cursor: pointer;
        }

        .prompt-submit:hover {
            filter: brightness(0.95);
        }

        .status-message {
            margin: 0 0 12px;
            padding: 9px 11px;
            border-radius: 10px;
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
            font-size: 0.92rem;
        }

        .error-message {
            margin: 8px 0 0;
            color: #991b1b;
            font-size: 0.9rem;
        }
    </style>

    <section class="prompt-panel">
        <p class="status-message" id="prompt-status" style="display: none;"></p>
        <p class="error-message" id="prompt-error" style="display: none;"></p>

        <form id="prompt-form">
            <label class="prompt-label" for="prompt">Enter prompt</label>
            <textarea class="prompt-input" id="prompt" name="prompt" required></textarea>

            <button class="prompt-submit" type="submit">Submit</button>
        </form>
    </section>

    <script>
        const promptForm = document.getElementById('prompt-form');
        const promptInput = document.getElementById('prompt');
        const promptStatus = document.getElementById('prompt-status');
        const promptError = document.getElementById('prompt-error');
        const submitButton = promptForm.querySelector('button[type="submit"]');

        promptForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const payload = {
                prompt: promptInput.value.trim(),
            };

            if (!payload.prompt) {
                promptError.textContent = 'Prompt is required.';
                promptError.style.display = 'block';
                return;
            }

            promptStatus.style.display = 'none';
            promptError.style.display = 'none';

            submitButton.disabled = true;
            submitButton.textContent = 'Submitting...';

            try {
                const response = await fetch('/api/generate-video', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                if (!response.ok) {
                    const errorPayload = await response.json().catch(() => ({}));
                    const message = errorPayload.message || 'Failed to submit prompt.';
                    throw new Error(message);
                }

                promptStatus.textContent = 'Prompt submitted.';
                promptStatus.style.display = 'block';
            } catch (error) {
                promptError.textContent = error.message;
                promptError.style.display = 'block';
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Submit';
            }
        });
    </script>
@endsection
