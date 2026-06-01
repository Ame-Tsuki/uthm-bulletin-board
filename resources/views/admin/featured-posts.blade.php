@extends('layouts.admin')

@section('content')
@php
    // Extract and sort only the featured posts for the preview
    $featuredPosts = $announcements->where('is_featured', 1)->sortBy('featured_order')->values();
@endphp

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Manage Featured Posts</h1>
        <p class="mt-2 text-gray-600">Select and manage announcements that appear in the featured carousel on the dashboard</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-star text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Featured Posts</p>
                    <p class="text-2xl font-bold">{{ $featuredCount }} / {{ $maxFeatured }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Published</p>
                    <p class="text-2xl font-bold">{{ $announcements->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-eye text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Admin Dashboard</p>
                    <a href="{{ route('dashboard') }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Dashboard →</a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold"><i class="fas fa-desktop text-gray-500 mr-2"></i>Live Carousel Preview</h2>
                <p class="text-sm text-gray-600">The carousel adapts to poster sizes while maintaining a professional 16:9 layout</p>
            </div>
        </div>
        <div class="p-4 md:p-8 bg-gray-200 flex justify-center">
            @if($featuredPosts->count() > 0)
                <div class="relative w-full max-w-5xl overflow-hidden rounded-2xl shadow-2xl bg-black aspect-video group">
                    <div id="carousel-track" class="flex h-full transition-transform duration-500 ease-in-out">
                        @foreach($featuredPosts as $post)
                            <div class="w-full h-full flex-shrink-0 relative bg-gray-900 overflow-hidden">
                                
                                @if($post->image)
                                    <img src="{{ asset('storage/' . $post->image) }}" class="absolute inset-0 w-full h-full object-cover blur-xl opacity-30 scale-110" aria-hidden="true" onerror="this.style.display='none'">
                                @endif

                                <div class="relative w-full h-full flex items-center justify-center p-2 pb-20">
                                    @if($post->image)
                                        <img src="{{ asset('storage/' . $post->image) }}" class="max-w-full max-h-full object-contain shadow-2xl rounded-sm" alt="Poster" onerror="this.src='https://placehold.co/800x450/1e293b/64748b?text=Image+Not+Found'">
                                    @else
                                        <img src="https://placehold.co/800x450/1e293b/64748b?text=No+Poster+Available" class="max-w-full max-h-full object-contain shadow-2xl rounded-sm">
                                    @endif
                                </div>
                                
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent p-4 md:p-8 pt-12 text-white">
                                    <div class="max-w-3xl">
                                        <span class="px-2 py-1 text-[10px] md:text-xs rounded mb-2 inline-block font-bold tracking-wider uppercase
                                            {{ $post->category === 'urgent' ? 'bg-red-500' : 
                                               ($post->category === 'academic' ? 'bg-blue-500' :
                                               ($post->category === 'events' ? 'bg-purple-500' : 'bg-gray-500')) }}">
                                            {{ $post->category ?? 'general' }}
                                        </span>
                                        <h3 class="text-lg md:text-3xl font-extrabold leading-tight drop-shadow-md truncate">{{ $post->title }}</h3>
                                        <p class="text-xs md:text-sm text-gray-300 mt-2 font-medium">
                                            <i class="fas fa-user-circle mr-1"></i> {{ $post->author?->name ?? 'Anonymous' }} <span class="mx-2">&bull;</span> {{ $post->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white w-10 h-10 md:w-12 md:h-12 rounded-full flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 border border-white/20 focus:opacity-100">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white w-10 h-10 md:w-12 md:h-12 rounded-full flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 border border-white/20 focus:opacity-100">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                    <div class="absolute bottom-4 right-4 md:right-8 flex space-x-2">
                        @foreach($featuredPosts as $index => $post)
                            <button onclick="goToSlide({{ $index }})" id="indicator-{{ $index }}" class="h-1.5 rounded-full transition-all duration-300 bg-white/30 hover:bg-white/70"></button>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="w-full max-w-4xl bg-white border-2 border-dashed border-gray-300 rounded-2xl p-16 text-center">
                    <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-images text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Carousel is Empty</h3>
                    <p class="text-gray-500 mt-2">Select "Feature" on an announcement below to add it to the carousel.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-semibold">All Announcements</h2>
            <p class="text-sm text-gray-600">Click "Feature" to add announcements to the carousel</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cover Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($announcements as $announcement)
                    <tr data-id="{{ $announcement->id }}">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ Str::limit($announcement->title, 50) }}</div>
                            <div class="text-xs text-gray-500">{{ $announcement->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-xs">{{ strtoupper(substr($announcement->author?->name ?? 'A', 0, 1)) }}</span>
                                </div>
                                <span>{{ $announcement->author?->name ?? 'Anonymous' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $announcement->priority === 'urgent' ? 'bg-red-100 text-red-800' : 
                                   ($announcement->priority === 'important' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($announcement->priority ?? 'normal') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $announcement->category === 'urgent' ? 'bg-red-100 text-red-800' : 
                                   ($announcement->category === 'academic' ? 'bg-blue-100 text-blue-800' :
                                   ($announcement->category === 'events' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($announcement->category ?? 'general') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($announcement->image)
                                <img src="{{ asset('storage/' . $announcement->image) }}" alt="Cover" class="w-10 h-10 rounded object-cover border border-gray-200" onerror="this.src='https://placehold.co/100x100/e2e8f0/64748b?text=No+Image'">
                            @else
                                <img src="https://placehold.co/100x100/e2e8f0/64748b?text=No+Image" alt="Default Cover" class="w-10 h-10 rounded object-cover border border-gray-200" title="No Image Provided">
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if(!$announcement->is_featured && $featuredCount >= $maxFeatured)
                                <button onclick="showToast('Maximum limit of {{ $maxFeatured }} featured posts reached. Unfeature a post first.', 'error')" 
                                        class="px-3 py-1 text-sm rounded bg-gray-200 text-gray-400 cursor-not-allowed transition-all" title="Limit reached">
                                    <i class="fas fa-star-o mr-1"></i> Feature
                                </button>
                            @else
                                <button onclick="toggleFeatured({{ $announcement->id }})" 
                                        class="px-3 py-1 text-sm rounded transition-all {{ $announcement->is_featured ? 'bg-green-500 text-white hover:bg-green-600' : 'bg-gray-300 text-gray-700 hover:bg-gray-400' }}">
                                    <i class="fas {{ $announcement->is_featured ? 'fa-star' : 'fa-star-o' }} mr-1"></i>
                                    {{ $announcement->is_featured ? 'Featured' : 'Feature' }}
                                </button>
                            @endif
                            
                            @if($announcement->is_featured && $announcement->featured_order)
                            <span class="ml-2 text-xs text-gray-500 font-medium">#{{ $announcement->featured_order }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('announcements.show', $announcement) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="toast" class="hidden fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300">
    <i class="fas fa-check-circle mr-2"></i>
    <span id="toastMessage"></span>
</div>

<script>
// --- Carousel Logic ---
let currentSlide = 0;
const totalSlides = {{ $featuredPosts->count() }};
const track = document.getElementById('carousel-track');
let slideInterval;

function updateCarousel() {
    if (!track) return;
    
    // Slide the track
    track.style.transform = `translateX(-${currentSlide * 100}%)`;
    
    // Update Indicators
    for (let i = 0; i < totalSlides; i++) {
        const indicator = document.getElementById(`indicator-${i}`);
        if (indicator) {
            if (i === currentSlide) {
                indicator.style.width = "2rem"; // Expanded width for active
                indicator.style.backgroundColor = "white";
            } else {
                indicator.style.width = "0.375rem"; // Normal dot width
                indicator.style.backgroundColor = "rgba(255, 255, 255, 0.3)";
            }
        }
    }
}

function nextSlide() {
    if (totalSlides === 0) return;
    currentSlide = (currentSlide + 1) % totalSlides;
    updateCarousel();
    resetInterval();
}

function prevSlide() {
    if (totalSlides === 0) return;
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    updateCarousel();
    resetInterval();
}

function goToSlide(index) {
    currentSlide = index;
    updateCarousel();
    resetInterval();
}

function resetInterval() {
    clearInterval(slideInterval);
    slideInterval = setInterval(nextSlide, 5000); // Auto-slide every 5 seconds
}

// Initialize carousel on load
document.addEventListener('DOMContentLoaded', () => {
    if (totalSlides > 0) {
        updateCarousel();
        slideInterval = setInterval(nextSlide, 5000);
    }
});

// --- Feature Toggle Logic ---
function toggleFeatured(id) {
    fetch('{{ route("admin.featured-posts.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message);
            setTimeout(() => location.reload(), 800); // Fast reload to update the preview
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Error toggling featured status', 'error');
        console.error(error);
    });
}

// --- Toast Logic ---
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    toastMessage.textContent = message;
    toast.classList.remove('hidden');
    
    if (type === 'error') {
        toast.classList.remove('bg-green-500');
        toast.classList.add('bg-red-500');
    } else {
        toast.classList.remove('bg-red-500');
        toast.classList.add('bg-green-500');
    }
    
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}
</script>
@endsection