<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - UTHM Digital Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .uthm-blue { color: #0056a6; }
        .bg-uthm-blue { background-color: #0056a6; }
        .border-uthm-blue { border-color: #0056a6; }
        .focus\:ring-uthm-blue:focus { --tw-ring-color: #0056a6; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-2xl">
            <!-- Header -->
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Create UTHM Account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Join the Digital Bulletin Board
                </p>
            </div>

            <!-- Registration Form -->
            <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
                @csrf
                
                <!-- UTHM ID -->
                <div>
                    <label for="uthm_id" class="block text-sm font-medium text-gray-700">UTHM ID *</label>
                    <input id="uthm_id" name="uthm_id" type="text" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uthm-blue focus:border-uthm-blue sm:text-sm"
                           placeholder="AI230102">
                    @error('uthm_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uthm-blue focus:border-uthm-blue sm:text-sm"
                           placeholder="Muhammad Amir Arif">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">UTHM Email *</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uthm-blue focus:border-uthm-blue sm:text-sm"
                           placeholder="ai230102@student.uthm.edu.my">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">I am a *</label>
                    <select id="role" name="role" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uthm-blue focus:border-uthm-blue sm:text-sm">
                        <option value="">Select Role</option>
                        <option value="student">Student</option>
                        <option value="staff">Staff</option>
                        <option value="club_admin">Club Admin</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Faculty (Conditional) -->
                <div id="faculty-field" style="display: none;">
                    <label for="faculty" class="block text-sm font-medium text-gray-700">Faculty *</label>
                    <select id="faculty" name="faculty" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uthm-blue focus:border-uthm-blue sm:text-sm">
                        <option value="">Select Faculty</option>
                        <option value="FCSIT">Faculty of Computer Science & IT</option>
                        <option value="FKMP">Faculty of Mechanical & Manufacturing</option>
                        <option value="FKAAB">Faculty of Architecture & Built Environment</option>
                        <option value="FPTP">Faculty of Technology & Plantation</option>
                    </select>
                    @error('faculty')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uthm-blue focus:border-uthm-blue sm:text-sm"
                           placeholder="Minimum 8 characters">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uthm-blue focus:border-uthm-blue sm:text-sm"
                           placeholder="Re-enter password">
                </div>

                <!-- Terms -->
                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required 
                           class="h-4 w-4 text-uthm-blue focus:ring-uthm-blue border-gray-300 rounded">
                    <label for="terms" class="ml-2 block text-sm text-gray-900">
                        I agree to the <a href="#" class="text-uthm-blue hover:underline">Terms</a>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-uthm-blue hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uthm-blue">
                        Create Account
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-uthm-blue hover:underline">
                            Sign in
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for Conditional Faculty Field -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const facultyField = document.getElementById('faculty-field');
            const facultySelect = document.getElementById('faculty');

            roleSelect.addEventListener('change', function() {
                if (this.value === 'student') {
                    facultyField.style.display = 'block';
                    facultySelect.required = true;
                } else {
                    facultyField.style.display = 'none';
                    facultySelect.required = false;
                    facultySelect.value = '';
                }
            });
        });
    </script>
</body>
</html>