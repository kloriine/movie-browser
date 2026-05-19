@extends('layouts.app')

@section('title', __('messages.favorites'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-heart text-danger"></i> {{ __('messages.favorites') }}</h2>
        <a href="{{ route('movies.index') }}" class="btn btn-primary">
            <i class="fas fa-search"></i> {{ __('messages.search_movies') }}
        </a>
    </div>
    
    @if($favorites->count() > 0)
    <div class="row" id="favorites-container">
        @foreach($favorites as $favorite)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 favorite-card" data-imdb-id="{{ $favorite->imdb_id }}">
            <div class="card h-100">
                <img src="{{ $favorite->poster !== 'N/A' ? $favorite->poster : 'https://via.placeholder.com/300x450?text=No+Poster' }}" 
                     class="card-img-top" 
                     alt="{{ $favorite->title }}"
                     style="height: 400px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title" title="{{ $favorite->title }}">{{ $favorite->title }}</h5>
                    <p class="card-text text-muted">
                        <small>{{ $favorite->year }} • {{ ucfirst($favorite->type ?? 'Movie') }}</small>
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('movies.show', $favorite->imdb_id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-info-circle"></i> {{ __('messages.view_details') }}
                        </a>
                        <button class="btn btn-sm btn-danger btn-remove-favorite" 
                                data-imdb-id="{{ $favorite->imdb_id }}"
                                data-title="{{ $favorite->title }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-heart-broken"></i>
        <h3>{{ __('messages.no_favorites') }}</h3>
        <p>{{ __('messages.no_favorites_description') }}</p>
        <a href="{{ route('movies.index') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-search"></i> {{ __('messages.search_movies') }}
        </a>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function showToast(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const toast = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas ${icon}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(toast);
        
        setTimeout(() => {
            toast.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    $('.btn-remove-favorite').on('click', function() {
        const $button = $(this);
        const imdbId = $button.data('imdb-id');
        const title = $button.data('title');
        
        if (confirm(`{{ __('messages.remove_from_favorites') }}: ${title}?`)) {
            $.ajax({
                url: `/favorites/${imdbId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Remove card with animation
                    $(`.favorite-card[data-imdb-id="${imdbId}"]`).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if there are no more favorites
                        if ($('.favorite-card').length === 0) {
                            location.reload();
                        }
                    });
                    
                    showToast(response.message);
                },
                error: function() {
                    showToast('{{ __("messages.error_occurred") }}', 'error');
                }
            });
        }
    });
</script>
@endsection