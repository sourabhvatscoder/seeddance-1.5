@extends('layouts.app')

@section('title', 'History')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Generation History</h1>
    </div>
    
    <p style="color: var(--muted); margin-top: -10px; margin-bottom: 20px;">Your past prompts and their video links.</p>

    <div style="overflow-x: auto; background: var(--panel); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead style="background: #f9fafb; border-bottom: 1px solid var(--border);">
                <tr>
                    <th style="padding: 14px 16px; color: var(--muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Date</th>
                    <th style="padding: 14px 16px; color: var(--muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Prompt</th>
                    <th style="padding: 14px 16px; color: var(--muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                    <th style="padding: 14px 16px; color: var(--muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Video Link</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $item)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 14px 16px; font-size: 0.9rem; color: var(--muted); white-space: nowrap;">
                            {{ $item->created_at->format('M d, Y H:i') }}
                        </td>
                        <td style="padding: 14px 16px; font-size: 0.95rem; max-width: 300px;">
                            {{ $item->prompt_text }}
                        </td>
                        <td style="padding: 14px 16px; font-size: 0.9rem;">
                            <span style="padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; 
                                @if($item->status === 'success') background: #dcfce7; color: #166534;
                                @elseif($item->status === 'error') background: #fee2e2; color: #991b1b;
                                @else background: #fef9c3; color: #854d0e; @endif">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td style="padding: 14px 16px; font-size: 0.9rem;">
                            @if($item->video_url)
                                <a href="{{ $item->video_url }}" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 500;">Watch Video &rarr;</a>
                            @elseif($item->seeddance_video_id)
                                <span style="color: var(--muted); font-size: 0.85rem;">Processing ID: {{ $item->seeddance_video_id }}</span>
                            @else
                                <span style="color: var(--muted); font-size: 0.85rem;">N/A</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 24px; text-align: center; color: var(--muted);">
                            No video generation history found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $history->links() }}
    </div>
@endsection