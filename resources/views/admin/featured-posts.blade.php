@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Manage Featured Posts</h1>
        <p class="mt-2 text-gray-600">Select and manage announcements that appear in the featured carousel on the dashboard</p>
    </div>

    <!-- Stats Cards -->
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
                    <p class="text-sm text-gray-600">Preview</p>
                    <a href="{{ route('dashboard') }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">View Dashboard →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements Table -->
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Featured Image</th>
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
                            @if($announcement->featured_image)
                            <div class="flex items-center space-x-2">
                                <img src="{{ $announcement->featured_image }}" alt="Preview" class="w-10 h-10 rounded object-cover" onerror="this.src='https://picsum.photos/id/20/50/50'">
                                <button onclick="editImage({{ $announcement->id }}, '{{ $announcement->featured_image }}')" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                            @else
                            <button onclick="editImage({{ $announcement->id }}, '')" class="text-gray-500 hover:text-blue-600 text-sm">
                                <i class="fas fa-plus-circle mr-1"></i> Add Image
                            </button>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="toggleFeatured({{ $announcement->id }})" 
                                    class="px-3 py-1 text-sm rounded transition-all {{ $announcement->is_featured ? 'bg-green-500 text-white hover:bg-green-600' : 'bg-gray-300 text-gray-700 hover:bg-gray-400' }}">
                                <i class="fas {{ $announcement->is_featured ? 'fa-star' : 'fa-star-o' }} mr-1"></i>
                                {{ $announcement->is_featured ? 'Featured' : 'Feature' }}
                            </button>
                            @if($announcement->is_featured && $announcement->featured_order)
                            <span class="ml-2 text-xs text-gray-500">#{{ $announcement->featured_order }}</span>
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

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Set Featured Image</h3>
            <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Image File</label>
            <input type="file" id="imageFile" accept="image/*" class="w-full border rounded-lg px-3 py-2" placeholder="https://example.com/image.jpg">
            <p class="text-xs text-gray-500 mt-1">Use any image URL. Recommended size: 600x400px</p>
        </div>
        <div class="mb-4">
            <div id="imagePreview" class="w-full h-40 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                <span class="text-gray-400">Preview will appear here</span>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <button onclick="closeImageModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
            <button onclick="saveImage()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="hidden fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <i class="fas fa-check-circle mr-2"></i>
    <span id="toastMessage"></span>
</div>

<script>
let currentAnnouncementId = null;
let currentImageUrl = null;

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
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Error toggling featured status', 'error');
        console.error(error);
    });
}

function editImage(id, currentUrl) {
    currentAnnouncementId = id;
    currentImageUrl = currentUrl;

    // Reset file input (important)
    document.getElementById('imageFile').value = '';

    document.getElementById('imageModal').style.display = 'flex';
    document.getElementById('imageModal').classList.remove('hidden');
    
    const preview = document.getElementById('imagePreview');

    if (currentUrl) {
        preview.innerHTML = `<img src="${currentUrl}" class="w-full h-full object-cover">`;
    } else {
        preview.innerHTML = '<span class="text-gray-400">Preview will appear here</span>';
    }
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
    document.getElementById('imageModal').classList.add('hidden');
    currentAnnouncementId = null;
}

function saveImage() {
    const fileInput = document.getElementById('imageFile');
    const file = fileInput.files[0];

    let formData = new FormData();
    formData.append('id', currentAnnouncementId);

    // IMPORTANT: send flag if no file (delete image)
    if (file) {
        formData.append('featured_image', file);
    } else {
        formData.append('remove_image', 1);
    }

    fetch('{{ route("admin.featured-posts.update-image") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message);
            closeImageModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed', 'error');
        }
    })
    .catch(error => {
        showToast('Error uploading image', 'error');
        console.error(error);
    });
}

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

// Preview image on URL input
document.getElementById('imageFile')?.addEventListener('change', function() {
    const file = this.files[0];
    const preview = document.getElementById('imagePreview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '<span class="text-gray-400">Preview will appear here</span>';
    }
});
</script>
@endsection