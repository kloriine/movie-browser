@extends('layouts.app')

@section('title', __('messages.movie_list'))

@section('content')
<div class="container">
    <!-- Search Box -->
    <div class="search-box">
        <h2 class="mb-4"><i class="fas fa-search"></i> {{ __('messages.search_movies') }}</h2>
        
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" 
                       class="form-control" 
                       id="search-input" 
                       placeholder="{{ __('messages.search_placeholder') }}"
                       value="movie">
            </div>
            
            <div class="col-md-2">
                <select class="form-select" id="type-filter">
                    <option value="all">{{ __('messages.all_types') }}</option>
                    <option value="movie">{{ __('messages.movie') }}</option>
                    <option value="series">{{ __('messages.series') }}</option>
                    <option value="episode">{{ __('messages.episode') }}</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <input type="number" 
                       class="form-control" 
                       id="year-filter" 
                       placeholder="{{ __('messages.year') }}"
                       min="1900"
                       max="{{ date('Y') }}">
            </div>
            
            <div class="col-md-2">
                <button class="btn btn-primary w-100" id="search-btn">
                    <i class="fas fa-search"></i> {{ __('messages.search') }}
                </button>
            </div>
        </div>
    </div>
    
    <!-- Movies Grid -->
    <div class="row" id="movies-container">
        <!-- Movies will be loaded here -->
    </div>
    
    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loading-spinner" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">{{ __('messages.loading') }}</span>
        </div>
        <p class="mt-2">{{ __('messages.loading_more') }}</p>
    </div>
    
    <!-- Empty State -->
    <div class="empty-state" id="empty-state" style="display: none;">
        <i class="fas fa-film"></i>
        <h3>{{ __('messages.no_movies_found') }}</h3>
        <p>{{ __('messages.search_placeholder') }}</p>
    </div>
    
    <!-- Scroll Sentinel for Infinite Scroll -->
    <div id="scroll-sentinel"></div>
</div>
@endsection

@section('scripts')
<script>
    let currentPage = 1;
    let isLoading = false;
    let hasMorePages = true;
    let currentSearch = 'movie';
    let currentType = 'all';
    let currentYear = '';
    
    // Toast notification function
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
    
    // Load movies function
    function loadMovies(page = 1, append = false) {
        if (isLoading) return;
        
        isLoading = true;
        $('#loading-spinner').show();
        
        $.ajax({
            url: '{{ route("movies.index") }}',
            method: 'GET',
            data: {
                search: currentSearch,
                page: page,
                type: currentType,
                year: currentYear
            },
            success: function(response) {
                if (response.Response === 'True') {
                    if (!append) {
                        $('#movies-container').empty();
                        $('#empty-state').hide();
                    }
                    
                    displayMovies(response.Search);
                    
                    // Check if there are more pages
                    const totalResults = parseInt(response.totalResults);
                    const moviesPerPage = 10;
                    hasMorePages = (page * moviesPerPage) < totalResults;
                    
                    // Update lazy load
                    lazyLoadInstance.update();
                } else {
                    if (!append) {
                        $('#movies-container').empty();
                        $('#empty-state').show();
                    }
                    hasMorePages = false;
                }
                
                isLoading = false;
                $('#loading-spinner').hide();
            },
            error: function() {
                showToast('{{ __("messages.error_occurred") }}', 'error');
                isLoading = false;
                $('#loading-spinner').hide();
            }
        });
    }
    
    // Display movies function
    function displayMovies(movies) {
        movies.forEach(function(movie) {
            const posterUrl = movie.Poster !== 'N/A' ? movie.Poster : 'https://via.placeholder.com/300x450?text=No+Poster';
            const isFavorite = movie.isFavorite || false;
            
            const movieCard = `
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4 movie-card">
                    <div class="card h-100">
                        <img data-src="${posterUrl}" 
                             class="card-img-top lazy" 
                             alt="${movie.Title}">
                        <div class="card-body">
                            <h5 class="card-title" title="${movie.Title}">${movie.Title}</h5>
                            <p class="card-text text-muted">
                                <small>${movie.Year} • ${movie.Type.charAt(0).toUpperCase() + movie.Type.slice(1)}</small>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="/movies/${movie.imdbID}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-info-circle"></i> {{ __('messages.view_details') }}
                                </a>
                                <button class="btn btn-sm btn-outline-danger btn-favorite ${isFavorite ? 'active' : ''}" 
                                        data-imdb-id="${movie.imdbID}"
                                        data-title="${movie.Title}"
                                        data-year="${movie.Year}"
                                        data-poster="${posterUrl}"
                                        data-type="${movie.Type}"
                                        title="${isFavorite ? '{{ __("messages.remove_from_favorites") }}' : '{{ __("messages.add_to_favorites") }}'}">
                                    <i class="${isFavorite ? 'fas' : 'far'} fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#movies-container').append(movieCard);
        });
    }
    
    // Toggle favorite function
    function toggleFavorite(button) {
        const $button = $(button);
        const imdbId = $button.data('imdb-id');
        const isActive = $button.hasClass('active');
        
        if (isActive) {
            // Remove from favorites
            $.ajax({
                url: `/favorites/${imdbId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $button.removeClass('active');
                    $button.find('i').removeClass('fas').addClass('far');
                    $button.attr('title', '{{ __("messages.add_to_favorites") }}');
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
                    $button.addClass('active');
                    $button.find('i').removeClass('far').addClass('fas');
                    $button.attr('title', '{{ __("messages.remove_from_favorites") }}');
                    showToast(response.message);
                },
                error: function() {
                    showToast('{{ __("messages.error_occurred") }}', 'error');
                }
            });
        }
    }
    
    // Search button click
    $('#search-btn').on('click', function() {
        currentSearch = $('#search-input').val() || 'movie';
        currentType = $('#type-filter').val();
        currentYear = $('#year-filter').val();
        currentPage = 1;
        hasMorePages = true;
        
        loadMovies(1, false);
    });
    
    // Enter key in search input
    $('#search-input').on('keypress', function(e) {
        if (e.which === 13) {
            $('#search-btn').click();
        }
    });
    
    // Favorite button click (using event delegation)
    $(document).on('click', '.btn-favorite', function(e) {
        e.preventDefault();
        toggleFavorite(this);
    });
    
    // Intersection Observer for infinite scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isLoading && hasMorePages) {
                currentPage++;
                loadMovies(currentPage, true);
            }
        });
    }, {
        rootMargin: '100px'
    });
    
    const sentinel = document.querySelector('#scroll-sentinel');
    if (sentinel) {
        observer.observe(sentinel);
    }
    
    // Initial load
    $(document).ready(function() {
        loadMovies(1, false);
    });
</script>
@endsection