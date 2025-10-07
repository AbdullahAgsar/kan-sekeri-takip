<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="bingbot" content="noindex, nofollow, noarchive, nosnippet">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kan Şekeri Takip</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
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
    <div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8 max-w-4xl">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                        <i class="fas fa-heartbeat text-red-500 mr-2 sm:mr-3"></i>
                        Kan Şekeri Takip
                    </h1>
                    <p class="text-gray-600 mt-1 sm:mt-2 text-sm sm:text-base">Günlük kan şekeri ölçümlerinizi takip edin</p>
                </div>
                <button onclick="toggleForm()" class="bg-primary text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-blue-600 transition duration-200 flex items-center w-full sm:w-auto justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    Yeni Ölçüm
                </button>
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

        <!-- Add/Edit Form -->
        <div id="glucoseForm" class="bg-white rounded-lg shadow-lg p-4 sm:p-6 mb-6 sm:mb-8 hidden">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-edit mr-2"></i>
                Kan Şekeri Ölçümü
            </h2>
            
            <form action="{{ route('glucose.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                               value="{{ old('value') }}"
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
                               value="{{ old('measurement_datetime', now()->format('Y-m-d\TH:i')) }}"
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
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                              placeholder="Ölçüm hakkında notlar...">{{ old('note') }}</textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_hungry" 
                           name="is_hungry" 
                           value="1"
                           {{ old('is_hungry') ? 'checked' : '' }}
                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="is_hungry" class="ml-2 block text-sm text-gray-700">
                        <i class="fas fa-utensils mr-1"></i>
                        Aç karnına ölçüm
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="toggleForm()" 
                            class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-200">
                        <i class="fas fa-times mr-1"></i>
                        İptal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition duration-200">
                        <i class="fas fa-save mr-1"></i>
                        Kaydet
                    </button>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        @if($glucoses->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Son Ölçüm</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $glucoses->first()->value }} mg/dL</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-calculator text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Ortalama</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($glucoses->avg('value'), 1) }} mg/dL</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-list text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Toplam Ölçüm</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $glucoses->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Chart Section -->
        @if($glucoses->count() > 0)
        <div class="bg-white rounded-lg shadow-lg mb-6 sm:mb-8">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">
                    <i class="fas fa-chart-line mr-2"></i>
                    Kan Şekeri Grafiği
                </h2>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Açlık ve tokluk ölçümlerinizin zaman içindeki değişimi</p>
            </div>
            <div class="p-4 sm:p-6">
                <div class="mb-4 flex flex-col sm:flex-row gap-2 sm:gap-4 justify-center items-center">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-red-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Aç karnına ölçüm</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Tok karnına ölçüm</span>
                    </div>
                </div>
                <div class="relative h-64 sm:h-80 md:h-96">
                    <canvas id="glucoseChart"></canvas>
                </div>
            </div>
        </div>
        @endif

        <!-- Glucose List -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">
                    <i class="fas fa-history mr-2"></i>
                    Ölçüm Geçmişi
                </h2>
            </div>
            
            @if($glucoses->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($glucoses as $glucose)
                        <div class="p-4 sm:p-6 hover:bg-gray-50 transition duration-200">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center space-x-3 sm:space-x-4">
                                    <div class="flex-shrink-0">
                                        @if($glucose->value < 70)
                                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                                            </div>
                                        @elseif($glucose->value > 180)
                                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-exclamation-circle text-orange-600"></i>
                                            </div>
                                        @else
                                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check-circle text-green-600"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-2xl font-bold text-gray-900">{{ $glucose->value }}</span>
                                            <span class="text-gray-500">mg/dL</span>
                                            @if($glucose->is_hungry)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-utensils mr-1"></i>
                                                    Aç
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $glucose->measurement_datetime->format('d.m.Y H:i') }}
                                        </p>
                                        @if($glucose->note)
                                            <p class="text-sm text-gray-500 mt-1">
                                                <i class="fas fa-sticky-note mr-1"></i>
                                                {{ $glucose->note }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('glucose.edit', $glucose->id) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition duration-200 p-2 rounded-lg hover:bg-blue-50"
                                       title="Düzenle">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>
                                    <button onclick="deleteGlucose({{ $glucose->id }})" 
                                            class="text-red-600 hover:text-red-800 transition duration-200 p-2 rounded-lg hover:bg-red-50"
                                            title="Sil">
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 sm:p-12 text-center">
                    <i class="fas fa-heartbeat text-gray-300 text-4xl sm:text-6xl mb-4"></i>
                    <h3 class="text-lg sm:text-xl font-medium text-gray-500 mb-2">Henüz ölçüm bulunmuyor</h3>
                    <p class="text-gray-400 mb-6 text-sm sm:text-base">İlk kan şekeri ölçümünüzü ekleyerek başlayın</p>
                    <button onclick="toggleForm()" class="bg-primary text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-blue-600 transition duration-200 w-full sm:w-auto">
                        <i class="fas fa-plus mr-2"></i>
                        İlk Ölçümü Ekle
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg p-4 sm:p-6 max-w-md w-full">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                <h3 class="text-lg font-medium text-gray-900">Ölçümü Sil</h3>
            </div>
            <p class="text-gray-600 mb-6">Bu ölçümü silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.</p>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-200 w-full sm:w-auto">
                    İptal
                </button>
                <form id="deleteForm" method="POST" class="inline w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 w-full">
                        Sil
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            const form = document.getElementById('glucoseForm');
            form.classList.toggle('hidden');
        }


        function deleteGlucose(id) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            form.action = `/glucose/${id}`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Auto-hide success messages
        setTimeout(function() {
            const successMessage = document.querySelector('.bg-green-100');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 5000);

        // Glucose Chart
        @if($glucoses->count() > 0)
        document.addEventListener('DOMContentLoaded', function() {
            // Wait a bit for Chart.js to load
            setTimeout(function() {
                const canvas = document.getElementById('glucoseChart');
                if (!canvas) {
                    console.error('Canvas element not found');
                    return;
                }
                
                if (typeof Chart === 'undefined') {
                    console.error('Chart.js is not loaded');
                    return;
                }
                
                const ctx = canvas.getContext('2d');
                const glucoseData = @json($chartData);
                
                // Sort data by datetime
                const sortedData = glucoseData.sort((a, b) => new Date(a.datetime) - new Date(b.datetime));
                
                // Create simple chart
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: sortedData.map(item => {
                            const date = new Date(item.datetime);
                            return date.toLocaleDateString('tr-TR', { 
                                day: '2-digit', 
                                month: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }),
                        datasets: [
                            {
                                label: 'Aç karnına ölçüm',
                                data: sortedData.map(item => item.is_hungry ? item.value : null),
                                borderColor: '#EF4444',
                                backgroundColor: '#EF4444',
                                borderWidth: 2,
                                pointRadius: 4,
                                tension: 0.1
                            },
                            {
                                label: 'Tok karnına ölçüm',
                                data: sortedData.map(item => !item.is_hungry ? item.value : null),
                                borderColor: '#3B82F6',
                                backgroundColor: '#3B82F6',
                                borderWidth: 2,
                                pointRadius: 4,
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Kan Şekeri Değerleri (mg/dL)',
                                font: {
                                    size: window.innerWidth < 640 ? 14 : 16
                                }
                            },
                            legend: {
                                labels: {
                                    font: {
                                        size: window.innerWidth < 640 ? 12 : 14
                                    },
                                    padding: window.innerWidth < 640 ? 10 : 20
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    font: {
                                        size: window.innerWidth < 640 ? 10 : 12
                                    },
                                    maxRotation: window.innerWidth < 640 ? 90 : 45,
                                    minRotation: window.innerWidth < 640 ? 90 : 45
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Kan Şekeri (mg/dL)',
                                    font: {
                                        size: window.innerWidth < 640 ? 12 : 14
                                    }
                                },
                                ticks: {
                                    font: {
                                        size: window.innerWidth < 640 ? 10 : 12
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            }, 100);
        });
        @endif
    </script>
</body>
</html>
