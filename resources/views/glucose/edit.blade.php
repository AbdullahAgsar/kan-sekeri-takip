<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kan Şekeri Düzenle - Kan Şekeri Takip</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#10B981',
                        danger: '#EF4444',
                        warning: '#F59E0B'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        <i class="fas fa-edit text-blue-500 mr-3"></i>
                        Kan Şekeri Düzenle
                    </h1>
                    <p class="text-gray-600 mt-2">Mevcut ölçümü düzenleyin</p>
                </div>
                <a href="{{ route('glucose.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Geri Dön
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Edit Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-edit mr-2"></i>
                Ölçüm Bilgilerini Düzenle
            </h2>
            
            <form action="{{ route('glucose.update', $glucose->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tint mr-1"></i>
                            Kan Şekeri Değeri (mg/dL)
                        </label>
                        <input type="number" 
                               id="value" 
                               name="value" 
                               step="0.1" 
                               min="0" 
                               max="1000"
                               value="{{ old('value', $glucose->value) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Örn: 120.5"
                               required>
                    </div>
                    
                    <div>
                        <label for="measurement_datetime" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-clock mr-1"></i>
                            Ölçüm Zamanı
                        </label>
                        <input type="datetime-local" 
                               id="measurement_datetime" 
                               name="measurement_datetime" 
                               value="{{ old('measurement_datetime', $glucose->measurement_datetime->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               required>
                    </div>
                </div>
                
                <div>
                    <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-1"></i>
                        Not (İsteğe bağlı)
                    </label>
                    <textarea id="note" 
                              name="note" 
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                              placeholder="Ölçüm hakkında notlar...">{{ old('note', $glucose->note) }}</textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_hungry" 
                           name="is_hungry" 
                           value="1"
                           {{ old('is_hungry', $glucose->is_hungry) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="is_hungry" class="ml-2 block text-sm text-gray-700">
                        <i class="fas fa-utensils mr-1"></i>
                        Aç karnına ölçüm
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('glucose.index') }}" 
                       class="px-6 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-200">
                        <i class="fas fa-times mr-1"></i>
                        İptal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition duration-200">
                        <i class="fas fa-save mr-1"></i>
                        Güncelle
                    </button>
                </div>
            </form>
        </div>

        <!-- Current Values Info -->
        <div class="bg-blue-50 rounded-lg p-4 mt-6">
            <h3 class="text-lg font-medium text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-2"></i>
                Mevcut Değerler
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-blue-700">Değer:</span>
                    <span class="text-blue-600">{{ $glucose->value }} mg/dL</span>
                </div>
                <div>
                    <span class="font-medium text-blue-700">Tarih:</span>
                    <span class="text-blue-600">{{ $glucose->measurement_datetime->format('d.m.Y H:i') }}</span>
                </div>
                <div>
                    <span class="font-medium text-blue-700">Aç karnına:</span>
                    <span class="text-blue-600">{{ $glucose->is_hungry ? 'Evet' : 'Hayır' }}</span>
                </div>
                @if($glucose->note)
                <div class="md:col-span-2">
                    <span class="font-medium text-blue-700">Not:</span>
                    <span class="text-blue-600">{{ $glucose->note }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Auto-hide success messages
        setTimeout(function() {
            const successMessage = document.querySelector('.bg-green-100');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 5000);
    </script>
</body>
</html>
