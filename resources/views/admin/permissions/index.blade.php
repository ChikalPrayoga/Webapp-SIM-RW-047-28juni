<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Permission Matrix (Read Only)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md shadow-sm mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi Pemetaan Izin (Permission)</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Halaman ini menyajikan tabel referensi silang (matrix) mengenai izin operasional sistem berdasarkan setiap peran (Role). Data bersifat final (read-only) pada environment ini.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r bg-gray-100">
                                    Izin Akses (Permission) \ Role
                                </th>
                                @foreach ($roles as $role)
                                <th class="px-3 py-3 text-center text-xs font-bold text-indigo-700 uppercase tracking-wider border-r whitespace-nowrap">
                                    {{ str_replace('_', ' ', $role->role_name) }}
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($allPermissions as $permissionName)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r bg-gray-50">
                                    {{ $permissionName }}
                                </td>
                                
                                @foreach ($roles as $role)
                                    @php
                                        // Cek apakah role ini memiliki permission yang sedang diiterasi
                                        // Secara eksplisit cek bypass SUPER_ADMIN atau pencarian collection
                                        $hasPermission = false;
                                        if ($role->role_name === 'SUPER_ADMIN') {
                                            $hasPermission = true;
                                        } else {
                                            $hasPermission = $role->permissions->contains('permission_name', $permissionName);
                                        }
                                    @endphp
                                    
                                    <td class="px-3 py-4 whitespace-nowrap text-center border-r">
                                        @if($hasPermission)
                                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-green-100 text-green-600">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center h-6 w-6 text-gray-300">
                                                -
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
