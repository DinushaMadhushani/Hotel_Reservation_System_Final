<!-- Staff Header with Tailwind CSS -->
<header class="bg-primary shadow-lg sticky top-0 z-50 transition-all duration-300">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Logo/Brand -->
            <div class="flex items-center space-x-2">
                <a href="../index.php" class="flex items-center group">
                    <div class="bg-accent/20 p-2 rounded-full mr-2 group-hover:bg-accent/30 transition-all duration-300">
                        <i class="fas fa-hotel text-accent text-2xl group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                    <span class="text-white font-bold text-xl group-hover:text-accent transition-colors duration-300">Hotel Portal</span>
                </a>
            </div>
            
            <!-- Navigation -->
            <nav class="hidden md:flex items-center space-x-8">
                <?php
                // Get current page filename
                $current_page = basename($_SERVER['PHP_SELF']);
                ?>
                <a href="./dashboard.php" class="text-gray-300 hover:text-accent transition-all duration-200 flex items-center py-2 border-b-2 <?= $current_page === 'dashboard.php' ? 'border-accent text-accent' : 'border-transparent' ?>">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                <a href="./task.php" class="text-gray-300 hover:text-accent transition-all duration-200 flex items-center py-2 border-b-2 <?= $current_page === 'task.php' ? 'border-accent text-accent' : 'border-transparent' ?>">
                    <i class="fas fa-tasks mr-2"></i> Tasks
                </a>
                <a href="./shedule.php" class="text-gray-300 hover:text-accent transition-all duration-200 flex items-center py-2 border-b-2 <?= $current_page === 'shedule.php' ? 'border-accent text-accent' : 'border-transparent' ?>">
                    <i class="fas fa-calendar-alt mr-2"></i> Schedule
                </a>
            </nav>
            
            <!-- User Menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center text-white hover:text-accent focus:outline-none transition-colors duration-200 group">
                    <span class="mr-2 hidden sm:inline"><?php echo isset($_SESSION['FullName']) ? htmlspecialchars($_SESSION['FullName']) : 'User'; ?></span>
                    <div class="bg-accent/20 p-1.5 rounded-full group-hover:bg-accent/30 transition-all duration-300">
                        <i class="fas fa-user-circle text-xl text-accent"></i>
                    </div>
                </button>
                
                <!-- Dropdown Menu -->
                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-3 w-56 bg-white rounded-lg shadow-xl py-2 z-50 hidden" id="userDropdown">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm leading-5 text-gray-500">Signed in as</p>
                        <p class="text-sm font-medium leading-5 text-gray-900 truncate"><?php echo isset($_SESSION['FullName']) ? htmlspecialchars($_SESSION['FullName']) : 'User'; ?></p>
                    </div>
                    <a href="./profile_manage.php" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 flex items-center">
                        <i class="fas fa-user-cog mr-3 text-gray-500"></i> Profile Settings
                    </a>
                    <a href="../auth/logout.php" class="block px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200 flex items-center">
                        <i class="fas fa-sign-out-alt mr-3 text-red-500"></i> Sign Out
                    </a>
                </div>
            </div>
            
            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button id="mobileMenuBtn" class="text-white hover:text-accent focus:outline-none transition-colors duration-200 p-2">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="md:hidden hidden pb-4 pt-2 border-t border-gray-700 mt-2 space-y-2">
            <a href="./dashboard.php" class="block py-2.5 px-3 text-gray-300 hover:text-accent hover:bg-primary-dark rounded-lg transition-colors duration-200 flex items-center <?= $current_page === 'dashboard.php' ? 'bg-accent/10 text-accent' : '' ?>">
                <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i> Dashboard
            </a>
            <a href="./task.php" class="block py-2.5 px-3 text-gray-300 hover:text-accent hover:bg-primary-dark rounded-lg transition-colors duration-200 flex items-center <?= $current_page === 'task.php' ? 'bg-accent/10 text-accent' : '' ?>">
                <i class="fas fa-tasks mr-3 w-5 text-center"></i> Tasks
            </a>
            <a href="./shedule.php" class="block py-2.5 px-3 text-gray-300 hover:text-accent hover:bg-primary-dark rounded-lg transition-colors duration-200 flex items-center <?= $current_page === 'shedule.php' ? 'bg-accent/10 text-accent' : '' ?>">
                <i class="fas fa-calendar-alt mr-3 w-5 text-center"></i> Schedule
            </a>
            <a href="./profile_manage.php" class="block py-2.5 px-3 text-gray-300 hover:text-accent hover:bg-primary-dark rounded-lg transition-colors duration-200 flex items-center <?= $current_page === 'profile_manage.php' ? 'bg-accent/10 text-accent' : '' ?>">
                <i class="fas fa-user-cog mr-3 w-5 text-center"></i> Profile
            </a>
            <div class="border-t border-gray-700 my-2"></div>
            <a href="../auth/logout.php" class="block py-2.5 px-3 text-red-400 hover:text-red-300 hover:bg-red-900/20 rounded-lg transition-colors duration-200 flex items-center">
                <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i> Sign Out
            </a>
        </div>
    </div>
</header>

<!-- Alpine.js for dropdown functionality -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Mobile menu toggle script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const userDropdown = document.getElementById('userDropdown');
        
        // Toggle mobile menu with animation
        mobileMenuBtn.addEventListener('click', function() {
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('animate-fadeIn');
                mobileMenuBtn.innerHTML = '<i class="fas fa-times text-xl"></i>';
            } else {
                mobileMenu.classList.add('hidden');
                mobileMenuBtn.innerHTML = '<i class="fas fa-bars text-xl"></i>';
            }
        });
        
        // Show/hide user dropdown without Alpine.js as fallback
        document.querySelectorAll('.relative button').forEach(button => {
            button.addEventListener('click', function() {
                userDropdown.classList.toggle('hidden');
            });
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.relative') && !userDropdown.classList.contains('hidden')) {
                userDropdown.classList.add('hidden');
            }
        });
        
        // Add shadow to header on scroll
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 10) {
                header.classList.add('shadow-xl');
                header.classList.remove('shadow-lg');
            } else {
                header.classList.remove('shadow-xl');
                header.classList.add('shadow-lg');
            }
        });
    });
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>
</script>