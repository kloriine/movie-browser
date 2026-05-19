@extends('layouts.app')

@section('title', $movie['Title'])

@section('styles')
<style>
    .movie-detail-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 0;
        margin-bottom: 2rem;
    }
    
    .movie-poster {
        max-width: 100%;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    
    .movie-info {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .rating-badge {
        display: inline-block;
        background: #ffc107;
        color: #000;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: bold;
        margin-right: 0.5rem;
    }
    
    .info-label {
        font-weight: 600;
        color: #6c757d;
        margin-right: 0.5rem;
    }
    
    .plot-section {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid #e9ecef;
    }
</style>
@endsection

@section('content')
<div class="movie-detail-header">
    <div class="container">
        <a href="{{ route('movies.index') }}" class="btn btn-light mb-3">
            <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
        </a>
        <h1 class="display-4">{{ $movie['Title'] }}</h1>
        <p class="lead">{{ $movie['Year'] }} • {{ $movie['Rated'] ?? 'N/A' }} • {{ $movie['Runtime'] ?? 'N/A' }}</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <img src="{{ $movie['Poster'] !== 'N/A' ? $movie['Poster'] : 'https://via.placeholder.com/300x450?text=No+Poster' }}" 
                 alt="{{ $movie['Title'] }}" 
                 class="movie-poster">
            
            <button class="btn btn-lg w-100 mt-3 btn-favorite {{ $isFavorite ? 'btn-danger' : 'btn-outline-danger' }}" 
                    id="favorite-btn"
                    data-imdb-id="{{ $movie['imdbID'] }}"
                    data-title="{{ $movie['Title'] }}"
                    data-year="{{ $movie['Year'] }}"
                    data-poster="{{ $movie['Poster'] }}"
                    data-type="{{ $movie['Type'] }}">
                <i class="{{ $isFavorite ? 'fas' : 'far' }} fa-heart"></i>
                <span id="favorite-text">
                    {{ $isFavorite ? __('messages.remove_from_favorites') : __('messages.add_to_favorites') }}
                </span>
            </button>
        </div>
        
        <div class="col-md-8">
            <div class="movie-info">
                @if(isset($movie['imdbRating']) && $movie['imdbRating'] !== 'N/A')
                <div class="mb-3">
                    <span class="rating-badge">
                        <i class="fas fa-star"></i> {{ $movie['imdbRating'] }}/10
                    </span>
                    @if(isset($movie['imdbVotes']))
                    <small class="text-muted">({{ $movie['imdbVotes'] }} votes)</small>
                    @endif
                </div>
                @endif
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p>
                            <span class="info-label">{{ __('messages.genre') }}:</span>
                            {{ $movie['Genre'] ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <span class="info-label">{{ __('messages.released') }}:</span>
                            {{ $movie['Released'] ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p>
                            <span class="info-label">{{ __('messages.director') }}:</span>
                            {{ $movie['Director'] ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <span class="info-label">{{ __('messages.writer') }}:</span>
                            {{ $movie['Writer'] ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p>
                        <span class="info-label">{{ __('messages.actors') }}:</span>
                        {{ $movie['Actors'] ?? 'N/A' }}
                    </p>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p>
                            <span class="info-label">{{ __('messages.language') }}:</span>
                            {{ $movie['Language'] ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <span class="info-label">{{ __('messages.country') }}:</span>
                            {{ $movie['Country'] ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                
                @if(isset($movie['Awards']) && $movie['Awards'] !== 'N/A')
                <div class="mb-3">
                    <p>
                        <span class="info-label">{{ __('messages.awards') }}:</span>
                        <span class="text-success">
                            <i class="fas fa-trophy"></i> {{ $movie['Awards'] }}
                        </span>
                    </p>
                </div>
                @endif
                
                @if(isset($movie['Plot']) && $movie['Plot'] !== 'N/A')
                <div class="plot-section">
                    <h4>{{ __('messages.plot') }}</h4>
                    <p class="lead">{{ $movie['Plot'] }}</p>
                </div>
                @endif
                
                @if(isset($movie['Ratings']) && count($movie['Ratings']) > 0)
                <div class="mt-4">
                    <h5>{{ __('messages.rating') }}</h5>
                    <div class="row">
                        @foreach($movie['Ratings'] as $rating)
                        <div class="col-md-4 mb-2">
                            <div class="card">
                                <div class="card-body text-center">
                                    <small class="text-muted">{{ $rating['Source'] }}</small>
                                    <h6 class="mb-0">{{ $rating['Value'] }}</h6>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
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
    
    $('#favorite-btn').on('click', function() {
        const $button = $(this);
        const imdbId = $button.data('imdb-id');
        const isActive = $button.hasClass('btn-danger');
        
        if (isActive) {
            // Remove from favorites
            $.ajax({
                url: `/favorites/${imdbId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $button.removeClass('btn-danger').addClass('btn-outline-danger');
                    $button.find('i').removeClass('fas').addClass('far');
                    $('#favorite-text').text('{{ __("messages.add_to_favorites") }}');
                    showToast(response.message);
                },
                error: function() {
                    showToast('{{ __("messages.error_occurred") }}', 'error');
                }
            });
        } else {
            // Add to favorites
            $.ajax({
                url: '{{ route("favorites.store") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    imdb_id: imdbId,
                    title: $button.data('title'),
                    year: $button.data('year'),
                    poster: $button.data('poster'),
                    type: $button.data('type')
                },
                success: function(response) {
                    $button.removeClass('btn-outline-danger').addClass('btn-danger');
                    $button.find('i').removeClass('far').addClass('fas');
                    $('#favorite-text').text('{{ __("messages.remove_from_favorites") }}');
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