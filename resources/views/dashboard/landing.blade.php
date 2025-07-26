<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Portal - Choose User Type</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-white min-h-screen">
    <div class="container mx-auto px-4">
        <div class="flex flex-col items-center justify-center min-h-screen">
            <div class="flex justify-center mb-6">
                <div class="logo-container w-48">
                        <img src="{{ asset('asset/images/swinlogo.png') }}" alt="Swinburne logo" class="w-full">
                </div>
            </div>
            <div class="bg-black p-8 rounded-lg shadow-lg w-full max-w-md">
                <h1 class="text-3xl font-bold text-center mb-8 text-white">Academic Portal</h1>
                <h2 class="text-xl text-center mb-6 text-black">Select User Type</h2>
                
                <div class="flex flex-col space-y-4">
                    <a href="{{ route('dashboard.academic-planner') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                        Student
                    </a>
                    
                    <a href="{{ route('dashboard.academic-department-head') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                        Department Head
                    </a>
                    
                    <a href="{{ route('dashboard.academic-director') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                        Academic Director
                    </a>
                </div>
                
                <p class="text-center text-white mt-8">
                    Please select your role to access the appropriate dashboard
                </p>
            </div>
        </div>
    </div>
</body>
</html>