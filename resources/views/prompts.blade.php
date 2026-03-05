@extends('layouts.app')

@section('title', 'Saved Prompts')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Saved Prompts</h1>
    </div>
    
    <p style="color: var(--muted); margin-top: -10px; margin-bottom: 20px;">Your favorite prompts and generated videos.</p>

    <div style="overflow-x: auto; background: var(--panel); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead style="background: #f9fafb; border-bottom: 1px solid var(--border);">
                <tr>
                    <th style="padding: 14px 16px; color: var(--muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Date</th>
                    <th style="padding: 14px 16px; color: var(--muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Prompt</th>
                    <th style="padding: 14px 16px; color: var(--muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Video Link</th>
                    <th style="padding: 14px 16px; color: var(--muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prompts as $item)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 14px 16px; font-size: 0.9rem; color: var(--muted); white-space: nowrap;">
                            {{ $item->created_at->format('M d, Y H:i') }}
                        </td>
                        <td style="padding: 14px 16px; font-size: 0.95rem; max-width: 300px;">
                            {{ $item->prompt_text }}
                        </td>
                        <td style="padding: 14px 16px; font-size: 0.9rem;">
                            @if($item->video_url)
                                <a href="{{ $item->video_url }}" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 500;">Watch Video &rarr;</a>
                            @else
                                <span style="color: var(--muted); font-size: 0.85rem;">Processing or N/A</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; text-align: center;">
                            
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 24px; text-align: center; color: var(--muted);">
                            You haven't saved any prompts yet. Go to your History to save some!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $prompts->links() }}
    </div>
@endsection