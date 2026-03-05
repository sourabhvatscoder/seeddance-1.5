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
<div style="display: flex; gap: 30px; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 300px;">
        <p style="color: var(--muted); margin-bottom: 20px;">Enter your prompt to generate an AI video.</p>
        <section class="prompt-panel">
            <p class="status-message" id="prompt-status" style="display: none;"></p>
            <p class="error-message" id="prompt-error" style="display: none;"></p>

            <form id="prompt-form">
                <label class="prompt-label" for="prompt">Enter prompt</label>
                <textarea class="prompt-input" id="prompt" name="prompt" required></textarea>

                <div style="display: flex; gap: 15px; margin-top: 15px;">
                    <div>
                        <label class="prompt-label" for="generate_audio">Include Sound?</label>
                        <select class="prompt-input" id="generate_audio" style="min-height: 40px; padding: 8px;">
                            <option value="1">Yes</option>
                            <option value="0" selected>No</option>
                        </select>
                    </div>

                    <div>
                        <label class="prompt-label" for="ratio">Aspect Ratio</label>
                        <select class="prompt-input" id="ratio" style="min-height: 40px; padding: 8px;">
                            <option value="21:9">21:9</option>
                            <option value="16:9" selected>16:9</option>
                            <option value="4:3">4:3</option>
                            <option value="1:1">1:1</option>
                            <option value="3:4">3:4</option>
                            <option value="9:16">9:16</option>
                        </select>
                    </div>

                    <div>
                        <label class="prompt-label" for="resolution">Resolution</label>
                        <select class="prompt-input" id="resolution" style="min-height: 40px; padding: 8px;">
                            <option value="480" selected>480p</option>
                            <option value="720">720p</option>
                            <option value="1080">1080p</option>
                        </select>
                    </div>

                    <div>
                        <label class="prompt-label" for="duration">Duration (Seconds)</label>
                        <select class="prompt-input" id="duration" style="min-height: 40px; padding: 8px;">
                            <option value="4" selected>4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                </div>

                <button class="prompt-submit" type="submit" style="margin-top: 20px;">Submit</button>
            </form>
            <!-- Video Player -->
            <div id="loading-area" style="display: none; background: #f9fafb; padding: 40px 24px; border-radius: 12px; border: 1px dashed var(--border); text-align: center; margin-top: 20px;">
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid var(--primary-soft); border-top-color: var(--primary); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 15px;"></div>
                <h3 style="margin: 0; color: var(--text);">Brewing your video...</h3>
                <p style="margin: 5px 0 0; color: var(--muted); font-size: 0.9rem;">This usually takes 1-2 minutes. Please don't close this tab.</p>
                <style>
                    @keyframes spin {
                        to {
                            transform: rotate(360deg);
                        }
                    }
                </style>
            </div>
            <div id="result-area" style="display: none; background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid var(--border); margin-top: 20px; box-shadow: var(--shadow);">
                <h3 style="margin: 0 0 15px 0; color: var(--text);">Success! Here is your video:</h3>

                <video id="result-video" controls autoplay style="width: 100%; border-radius: 8px; background: #000; margin-bottom: 20px;"></video>

                <div style="display: flex; gap: 10px;">
                    <a id="download-btn" href="#" download="generated-video.mp4" target="_blank" style="flex: 1; display: inline-block; text-align: center; background: #10b981; color: white; text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 600;">
                        ↓ Download Video
                    </a>
                    <a id="save-prompt-btn" href="#" data-id="VIDEO_ID_HERE" style="flex: 1; display: inline-block; text-align: center; background: var(--primary); color: white; text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 600;">
                        ♥ Save to Prompts
                    </a>
                </div>
            </div>
        </section>
    </div>

    <div style="width: 300px; display: flex; flex-direction: column; gap: 20px;">

        <div style="background: var(--panel); border: 1px solid var(--border); border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);">
            <h3 style="margin: 0 0 15px 0; color: var(--text); font-size: 1rem;">Overview</h3>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="color: var(--muted); font-size: 0.9rem;">Total Generations</span>
                <span style="font-weight: 600;">{{ $totalVideos }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--muted); font-size: 0.9rem;">Currently Processing</span>
                <span style="font-weight: 600; color: var(--primary);">{{ $processingVideos }}</span>
            </div>
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border); text-align: center;">
                <a href="https://console.byteplus.com/finance/overview" style="display: inline-block; padding: 10px 16px; background: var(--primary); color: #ffffff; border-radius: 8px; font-size: 0.9rem; font-weight: 500; text-decoration: none; transition: filter 0.2s ease;">Manage Billing</a>
            </div>
        </div>
    </div>
</div>
<script>
    const promptForm = document.getElementById('prompt-form');
    const promptInput = document.getElementById('prompt');
    const promptError = document.getElementById('prompt-error');
    const submitButton = promptForm.querySelector('button[type="submit"]');
    const loadingArea = document.getElementById('loading-area');
    const resultArea = document.getElementById('result-area');
    const resultVideo = document.getElementById('result-video');
    const downloadBtn = document.getElementById('download-btn');
    const saveForm = document.getElementById('save-form');
    const savePromptBtn = document.getElementById('save-prompt-btn');

    promptForm.addEventListener('submit', async function(event) {
        event.preventDefault();

        const payload = {
            prompt: promptInput.value.trim(),
            generate_audio: document.getElementById('generate_audio').value === '1', // converts to boolean
            ratio: document.getElementById('ratio').value,
            resolution: parseInt(document.getElementById('resolution').value),
            duration: parseInt(document.getElementById('duration').value),
        };

        if (!payload.prompt) {
            promptError.textContent = 'Prompt is required.';
            promptError.style.display = 'block';
            return;
        }

        submitButton.disabled = true;
        submitButton.textContent = 'Submitting...';

        loadingArea.style.display = 'block';
        resultArea.style.display = 'none';

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

            const responsePayload = await response.json();

            fetchVideoStatus(responsePayload.seeddance_video_id);
        } catch (error) {
            promptError.textContent = error.message;
            promptError.style.display = 'block';
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Submit';
        }
    });

    if (savePromptBtn) {
        savePromptBtn.addEventListener('click', function() {
            const btn = this;
            const videoId = btn.getAttribute('data-id');

            // Disable button and update text to show processing state
            btn.disabled = true;
            btn.innerText = 'Saving...';

            // Call the newly created API
            fetch(`/api/save-prompt/${videoId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI on success without page reload
                        btn.innerText = 'Saved!';
                    } else {
                        alert(data.message || 'Failed to save prompt.');
                        // Re-enable button on failure
                        btn.disabled = false;
                        btn.innerText = 'Save to Prompts';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving.');
                    btn.disabled = false;
                    btn.innerText = 'Save to Prompts';
                });
        });
    }

    async function fetchVideoStatus(videoId) {
        try {
            submitButton.disabled = true;
            submitButton.textContent = 'Generating video...';

            const response = await fetch(`/api/get-video-status/${videoId}`);
            const data = await response.json();

            if (data.status === 'success') {
                console.log('Video is ready:', data.video_url);
                submitButton.disabled = false;
                submitButton.textContent = 'Submit';
                // Display the video to the user
                loadingArea.style.display = 'none';
                resultArea.style.display = 'block';
                resultVideo.src = data.video_url;
                downloadBtn.href = data.video_url;
                resultVideo.load();
                // Update save button data-id for later use
                savePromptBtn.setAttribute('data-id', videoId);
            } else if (data.status === 'processing') {
                console.log('Still processing... checking again in 5 seconds.');
                setTimeout(() => fetchVideoStatus(videoId), 2000);
            } else {
                console.error('Video generation failed:', data.message);
            }
        } catch (error) {
            console.error('Network or server error:', error);
        }
    }
</script>
@endsection