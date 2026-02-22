// ========== TIMER CLASS ==========
class Timer {
    constructor() {
        // Default to indefinite mode
        this.mode = 'indefinite'; // 'indefinite' or 'pomodoro'
        this.initialDuration = 25 * 60; // 25 minutes in seconds (for pomodoro)
        this.currentTime = 0; // Start at 00:00 for indefinite mode
        this.intervalId = null;
        this.isRunning = false;
        this.isPaused = false;
        
        // Pomodoro specific
        this.pomodoroPhase = 'focus'; // 'focus' or 'break'
        this.focusDuration = 25 * 60;
        this.breakDuration = 5 * 60;
        this.pomodoroCycles = 0;
        this.autoStartBreaks = true;
        this.soundPreference = 'bell';
        
        // DOM elements
        this.timerDisplay = document.getElementById('timer');
        this.initialControls = document.getElementById('initialControls');
        this.activeControls = document.getElementById('activeControls');
        this.pausedControls = document.getElementById('pausedControls');
        
        // Buttons
        this.startBtn = document.getElementById('startBtn');
        this.pauseBtn = document.getElementById('pauseBtn');
        this.stopBtn = document.getElementById('stopBtn');
        this.resetBtn = document.getElementById('resetBtn');
        this.pausedResumeBtn = document.getElementById('pausedResumeBtn');
        this.pausedStopBtn = document.getElementById('pausedStopBtn');
        this.pausedResetBtn = document.getElementById('pausedResetBtn');
        
        // Settings buttons
        this.settingsBtns = document.querySelectorAll('#settingsBtn, #activeSettingsBtn, #pausedSettingsBtn');
        this.fullscreenBtns = document.querySelectorAll('#fullscreenBtn, #activeFullscreenBtn, #pausedFullscreenBtn');
        
        // Settings modal
        this.settingsModal = document.getElementById('timerSettingsModal');
        this.closeSettingsBtn = document.getElementById('closeTimerSettingsBtn');
        this.cancelSettingsBtn = document.getElementById('cancelTimerSettingsBtn');
        this.saveSettingsBtn = document.getElementById('saveTimerSettingsBtn');
        
        // Settings inputs - check if they exist before assigning
        this.timerMode = document.getElementById('timerMode');
        this.focusDurationInput = document.getElementById('focusDuration');
        this.breakDurationInput = document.getElementById('breakDuration');
        this.timerSoundInput = document.getElementById('timerSound');
        this.autoStartBreaksInput = document.getElementById('autoStartBreaks');
        this.pomodoroSettings = document.getElementById('pomodoroSettings');
        this.indefiniteSettings = document.getElementById('indefiniteSettings');
        
        this.init();
        this.loadSettings();
        this.updateDisplay();
    }
    
    init() {
        // Event listeners
        if (this.startBtn) this.startBtn.addEventListener('click', () => this.start());
        if (this.pauseBtn) this.pauseBtn.addEventListener('click', () => this.pause());
        if (this.stopBtn) this.stopBtn.addEventListener('click', () => this.stop());
        if (this.resetBtn) this.resetBtn.addEventListener('click', () => this.reset());
        if (this.pausedResumeBtn) this.pausedResumeBtn.addEventListener('click', () => this.resume());
        if (this.pausedStopBtn) this.pausedStopBtn.addEventListener('click', () => this.stop());
        if (this.pausedResetBtn) this.pausedResetBtn.addEventListener('click', () => this.reset());
        
        // Settings
        this.settingsBtns.forEach(btn => {
            if (btn) btn.addEventListener('click', () => this.openSettings());
        });
        
        if (this.closeSettingsBtn) this.closeSettingsBtn.addEventListener('click', () => this.closeSettings());
        if (this.cancelSettingsBtn) this.cancelSettingsBtn.addEventListener('click', () => this.closeSettings());
        if (this.saveSettingsBtn) this.saveSettingsBtn.addEventListener('click', () => this.saveSettings());
        
        // Mode toggle
        if (this.timerMode) {
            this.timerMode.addEventListener('change', () => this.toggleModeSettings());
        }
        
        // Fullscreen
        this.fullscreenBtns.forEach(btn => {
            if (btn) btn.addEventListener('click', () => this.toggleFullscreen());
        });
        
        // Close modal when clicking outside
        if (this.settingsModal) {
            this.settingsModal.addEventListener('click', (e) => {
                if (e.target === this.settingsModal) {
                    this.closeSettings();
                }
            });
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space' && this.isRunning) {
                e.preventDefault();
                if (this.isPaused) {
                    this.resume();
                } else {
                    this.pause();
                }
            } else if (e.code === 'Escape' && this.settingsModal && this.settingsModal.classList.contains('show')) {
                this.closeSettings();
            }
        });
    }
    
    toggleModeSettings() {
        if (!this.timerMode) return;
        
        const mode = this.timerMode.value;
        
        if (mode === 'pomodoro') {
            if (this.pomodoroSettings) this.pomodoroSettings.style.display = 'block';
            if (this.indefiniteSettings) this.indefiniteSettings.style.display = 'none';
        } else {
            if (this.pomodoroSettings) this.pomodoroSettings.style.display = 'none';
            if (this.indefiniteSettings) this.indefiniteSettings.style.display = 'block';
        }
    }
    
    start() {
        if (this.isRunning) return;
        
        this.isRunning = true;
        this.isPaused = false;
        
        // Show active controls
        if (this.initialControls) this.initialControls.style.display = 'none';
        if (this.activeControls) this.activeControls.style.display = 'flex';
        if (this.pausedControls) this.pausedControls.style.display = 'none';
        
        // Start timer
        this.intervalId = setInterval(() => {
            if (this.mode === 'indefinite') {
                // Indefinite mode - count up from 00:00
                this.currentTime++;
                this.updateDisplay();
                document.title = this.formatTime(this.currentTime) + ' - Kaizen';
            } else {
                // Pomodoro mode - count down
                if (this.currentTime <= 0) {
                    this.switchPomodoroPhase();
                } else {
                    this.currentTime--;
                    this.updateDisplay();
                    document.title = this.formatTime(this.currentTime) + ' - Kaizen';
                }
            }
        }, 1000);
        
        document.body.classList.add('timer-active');
    }
    
    pause() {
        if (!this.isRunning || this.isPaused) return;
        
        this.isPaused = true;
        clearInterval(this.intervalId);
        
        // Show paused controls
        if (this.initialControls) this.initialControls.style.display = 'none';
        if (this.activeControls) this.activeControls.style.display = 'none';
        if (this.pausedControls) this.pausedControls.style.display = 'flex';
        
        document.body.classList.remove('timer-active');
        document.body.classList.add('timer-paused');
        document.title = 'Paused - Kaizen';
    }
    
    resume() {
        if (!this.isRunning || !this.isPaused) return;
        
        this.isPaused = false;
        
        // Show active controls
        if (this.initialControls) this.initialControls.style.display = 'none';
        if (this.activeControls) this.activeControls.style.display = 'flex';
        if (this.pausedControls) this.pausedControls.style.display = 'none';
        
        // Resume timer
        this.intervalId = setInterval(() => {
            if (this.mode === 'indefinite') {
                // Indefinite mode - count up
                this.currentTime++;
                this.updateDisplay();
                document.title = this.formatTime(this.currentTime) + ' - Kaizen';
            } else {
                // Pomodoro mode - count down
                if (this.currentTime <= 0) {
                    this.switchPomodoroPhase();
                } else {
                    this.currentTime--;
                    this.updateDisplay();
                    document.title = this.formatTime(this.currentTime) + ' - Kaizen';
                }
            }
        }, 1000);
        
        document.body.classList.add('timer-active');
        document.body.classList.remove('timer-paused');
    }
    
    stop() {
        this.isRunning = false;
        this.isPaused = false;
        clearInterval(this.intervalId);
        
        // Show initial controls
        if (this.initialControls) this.initialControls.style.display = 'flex';
        if (this.activeControls) this.activeControls.style.display = 'none';
        if (this.pausedControls) this.pausedControls.style.display = 'none';
        
        // Reset based on mode
        if (this.mode === 'indefinite') {
            this.currentTime = 0; // Reset to 00:00
        } else {
            this.currentTime = this.focusDuration;
            this.pomodoroPhase = 'focus';
        }
        
        this.updateDisplay();
        
        document.body.classList.remove('timer-active', 'timer-paused');
        document.title = 'Kaizen';
    }
    
    reset() {
        if (this.mode === 'indefinite') {
            this.currentTime = 0; // Reset to 00:00
        } else {
            this.currentTime = this.focusDuration;
            this.pomodoroPhase = 'focus';
        }
        
        this.updateDisplay();
        document.title = this.formatTime(this.currentTime) + ' - Kaizen';
        
        // If timer is paused, show active controls
        if (this.isPaused) {
            if (this.initialControls) this.initialControls.style.display = 'none';
            if (this.activeControls) this.activeControls.style.display = 'flex';
            if (this.pausedControls) this.pausedControls.style.display = 'none';
            this.isPaused = false;
            document.body.classList.add('timer-active');
            document.body.classList.remove('timer-paused');
        }
        
        // If timer is running, it continues counting from reset value
    }
    
    switchPomodoroPhase() {
        // Play sound for phase change
        this.playSound('phase-change');
        
        if (this.pomodoroPhase === 'focus') {
            // Switch to break
            this.pomodoroPhase = 'break';
            this.currentTime = this.breakDuration;
            this.pomodoroCycles++;
            
            // Show notification
            this.showNotification('Focus Complete!', 'Time for a break.');
            
            // Update display to show it's break time
            document.body.classList.add('break-mode');
        } else {
            // Switch to focus
            this.pomodoroPhase = 'focus';
            this.currentTime = this.focusDuration;
            
            // Show notification
            this.showNotification('Break Complete!', 'Back to focus.');
            
            // Remove break mode class
            document.body.classList.remove('break-mode');
        }
        
        this.updateDisplay();
        
        // Auto-start next phase if enabled
        if (!this.autoStartBreaks && this.pomodoroPhase === 'break') {
            // Pause at break if auto-start is disabled
            this.pause();
        } else if (this.autoStartBreaks) {
            // Continue automatically
            // Timer continues running
        }
    }
    
    formatTime(seconds) {
        if (this.mode === 'indefinite') {
            // For indefinite mode, show hours if needed
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            if (hours > 0) {
                return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            } else {
                return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
        } else {
            // Pomodoro mode - standard MM:SS
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
        }
    }
    
    updateDisplay() {
        if (this.timerDisplay) {
            this.timerDisplay.textContent = this.formatTime(this.currentTime);
        }
    }
    
    openSettings() {
        if (!this.settingsModal) return;
        
        // Populate current values
        if (this.timerMode) this.timerMode.value = this.mode;
        if (this.focusDurationInput) this.focusDurationInput.value = Math.floor(this.focusDuration / 60);
        if (this.breakDurationInput) this.breakDurationInput.value = Math.floor(this.breakDuration / 60);
        if (this.autoStartBreaksInput) this.autoStartBreaksInput.checked = this.autoStartBreaks;
        if (this.timerSoundInput) this.timerSoundInput.value = this.soundPreference;
        
        // Show/hide appropriate settings
        this.toggleModeSettings();
        
        this.settingsModal.classList.add('show');
    }
    
    closeSettings() {
        if (this.settingsModal) {
            this.settingsModal.classList.remove('show');
        }
    }
    
    saveSettings() {
        if (!this.timerMode) return;
        
        const newMode = this.timerMode.value;
        
        if (newMode === 'pomodoro') {
            // Save pomodoro settings
            if (this.focusDurationInput) {
                this.focusDuration = (parseInt(this.focusDurationInput.value) || 25) * 60;
            }
            if (this.breakDurationInput) {
                this.breakDuration = (parseInt(this.breakDurationInput.value) || 5) * 60;
            }
            if (this.autoStartBreaksInput) {
                this.autoStartBreaks = this.autoStartBreaksInput.checked;
            }
            
            // Only reset if changing mode or timer not running
            if (this.mode !== 'pomodoro' || !this.isRunning) {
                this.mode = 'pomodoro';
                this.currentTime = this.focusDuration;
                this.pomodoroPhase = 'focus';
                this.pomodoroCycles = 0;
                document.body.classList.remove('break-mode');
            }
        } else {
            // Indefinite mode
            if (this.mode !== 'indefinite' || !this.isRunning) {
                this.mode = 'indefinite';
                this.currentTime = 0; // Start at 00:00
            }
        }
        
        // Save sound preference
        if (this.timerSoundInput) {
            this.soundPreference = this.timerSoundInput.value;
        }
        
        // Save to localStorage
        localStorage.setItem('timerSettings', JSON.stringify({
            mode: this.mode,
            focusDuration: Math.floor(this.focusDuration / 60),
            breakDuration: Math.floor(this.breakDuration / 60),
            sound: this.soundPreference,
            autoStartBreaks: this.autoStartBreaks
        }));
        
        this.updateDisplay();
        this.closeSettings();
    }
    
    loadSettings() {
        const saved = localStorage.getItem('timerSettings');
        if (saved) {
            try {
                const settings = JSON.parse(saved);
                
                if (settings.mode) {
                    this.mode = settings.mode;
                }
                
                if (settings.focusDuration) {
                    this.focusDuration = settings.focusDuration * 60;
                }
                
                if (settings.breakDuration) {
                    this.breakDuration = settings.breakDuration * 60;
                }
                
                if (settings.autoStartBreaks !== undefined) {
                    this.autoStartBreaks = settings.autoStartBreaks;
                }
                
                if (settings.sound) {
                    this.soundPreference = settings.sound;
                }
                
                // Set current time based on mode
                if (this.mode === 'indefinite') {
                    this.currentTime = 0; // Start at 00:00
                } else {
                    this.currentTime = this.focusDuration;
                    this.pomodoroPhase = 'focus';
                }
                
                // Update settings inputs if they exist
                if (this.focusDurationInput) {
                    this.focusDurationInput.value = Math.floor(this.focusDuration / 60);
                }
                if (this.breakDurationInput) {
                    this.breakDurationInput.value = Math.floor(this.breakDuration / 60);
                }
                if (this.timerSoundInput) {
                    this.timerSoundInput.value = this.soundPreference;
                }
                if (this.autoStartBreaksInput) {
                    this.autoStartBreaksInput.checked = this.autoStartBreaks;
                }
                
                this.updateDisplay();
            } catch (e) {
                console.error('Error loading settings:', e);
            }
        }
    }
    
    playSound(type = 'complete') {
        const sound = this.soundPreference || 'bell';
        if (sound === 'none') return;
        
        // Create audio context for sounds
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        if (type === 'phase-change') {
            // Softer sound for phase changes
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(660, audioContext.currentTime);
            
            gainNode.gain.setValueAtTime(0.05, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.start();
            oscillator.stop(audioContext.currentTime + 0.5);
        } else {
            // Full completion sound
            if (sound === 'bell') {
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(880, audioContext.currentTime);
                
                gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 1);
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.start();
                oscillator.stop(audioContext.currentTime + 1);
            } else if (sound === 'digital') {
                for (let i = 0; i < 3; i++) {
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();
                    
                    oscillator.type = 'square';
                    oscillator.frequency.setValueAtTime(660, audioContext.currentTime + i * 0.3);
                    
                    gainNode.gain.setValueAtTime(0.1, audioContext.currentTime + i * 0.3);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + i * 0.3 + 0.2);
                    
                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);
                    
                    oscillator.start(audioContext.currentTime + i * 0.3);
                    oscillator.stop(audioContext.currentTime + i * 0.3 + 0.2);
                }
            } else if (sound === 'gentle') {
                const notes = [523.25, 659.25, 783.99];
                notes.forEach((freq, i) => {
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();
                    
                    oscillator.type = 'sine';
                    oscillator.frequency.setValueAtTime(freq, audioContext.currentTime + i * 0.2);
                    
                    gainNode.gain.setValueAtTime(0.05, audioContext.currentTime + i * 0.2);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + i * 0.2 + 0.5);
                    
                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);
                    
                    oscillator.start(audioContext.currentTime + i * 0.2);
                    oscillator.stop(audioContext.currentTime + i * 0.2 + 0.5);
                });
            }
        }
    }
    
    showNotification(title, body) {
        if (!('Notification' in window)) return;
        
        if (Notification.permission === 'granted') {
            new Notification(title, {
                body: body,
                icon: '/assets/images/icon.png',
                silent: true
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission();
        }
    }
    
    toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }
}

// ========== TASK MANAGEMENT ==========

// Update task counts
function updateTaskCounts() {
    const todoTasks = document.querySelectorAll('#todo-tasks .task-card:not(.empty-state)').length;
    const progressTasks = document.querySelectorAll('#progress-tasks .task-card:not(.empty-state)').length;
    const doneTasks = document.querySelectorAll('#done-tasks .task-card:not(.empty-state)').length;
    
    const todoCount = document.getElementById('todo-count');
    const progressCount = document.getElementById('progress-count');
    const doneCount = document.getElementById('done-count');
    
    if (todoCount) todoCount.textContent = todoTasks;
    if (progressCount) progressCount.textContent = progressTasks;
    if (doneCount) doneCount.textContent = doneTasks;
}

// Move task
function moveTask(taskId, newStatus) {
    fetch('tasks/move_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `task_id=${taskId}&status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error moving task: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error moving task');
    });
}

// Delete task
function deleteTask(taskId) {
    if (confirm('Are you sure you want to delete this task?')) {
        fetch('tasks/delete_task.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `task_id=${taskId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting task: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting task');
        });
    }
}

// Edit task
function editTask(taskId) {
    fetch(`tasks/get_task.php?task_id=${taskId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const taskIdInput = document.getElementById('taskId');
                const taskTitle = document.getElementById('taskTitle');
                const taskDescription = document.getElementById('taskDescription');
                const taskPriority = document.getElementById('taskPriority');
                const taskDueDate = document.getElementById('taskDueDate');
                const taskStatus = document.getElementById('taskStatus');
                const modalTitle = document.getElementById('modalTitle');
                const taskModal = document.getElementById('taskModal');
                
                if (taskIdInput) taskIdInput.value = data.task.id;
                if (taskTitle) taskTitle.value = data.task.title;
                if (taskDescription) taskDescription.value = data.task.description || '';
                if (taskPriority) taskPriority.value = data.task.priority;
                if (taskDueDate) taskDueDate.value = data.task.due_date || '';
                if (taskStatus) taskStatus.value = data.task.status;
                
                // Check the correct tags
                document.querySelectorAll('input[name="tags[]"]').forEach(checkbox => {
                    checkbox.checked = data.task.tags && data.task.tags.includes(checkbox.value);
                });
                
                if (modalTitle) modalTitle.innerHTML = '<i data-lucide="edit"></i> Edit Task';
                if (taskModal) taskModal.classList.add('show');
                
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            } else {
                alert('Error loading task: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading task. Please check the console for details.');
        });
}

// ========== UI CONTROLLER ==========
class UIController {
    constructor() {
        this.focusBtn = document.getElementById('focusBtn');
        this.taskBtn = document.getElementById('taskBtn');
        this.musicBtn = document.getElementById('musicBtn');
        this.timerView = document.getElementById('timerView');
        this.taskView = document.getElementById('taskView');
        this.themeToggle = document.getElementById('themeToggle');
        this.openAddModalBtn = document.getElementById('openAddModalBtn');
        this.taskModal = document.getElementById('taskModal');
        this.closeModalBtn = document.getElementById('closeModalBtn');
        this.cancelModalBtn = document.getElementById('cancelModalBtn');
        this.taskForm = document.getElementById('taskForm');
        this.addNewTagBtn = document.getElementById('addNewTagBtn');
        this.searchInput = document.getElementById('searchTasks');
        
        this.init();
    }
    
    init() {
        // Navigation
        if (this.focusBtn) this.focusBtn.addEventListener('click', () => this.showView('focus'));
        if (this.taskBtn) this.taskBtn.addEventListener('click', () => this.showView('task'));
        if (this.musicBtn) this.musicBtn.addEventListener('click', () => this.showView('music'));
        
        // Theme
        if (this.themeToggle) this.themeToggle.addEventListener('click', () => this.toggleTheme());
        
        // Task modal
        if (this.openAddModalBtn) this.openAddModalBtn.addEventListener('click', () => this.openTaskModal());
        if (this.closeModalBtn) this.closeModalBtn.addEventListener('click', () => this.closeTaskModal());
        if (this.cancelModalBtn) this.cancelModalBtn.addEventListener('click', () => this.closeTaskModal());
        
        if (this.taskModal) {
            this.taskModal.addEventListener('click', (e) => {
                if (e.target === this.taskModal) {
                    this.closeTaskModal();
                }
            });
        }
        
        if (this.taskForm) this.taskForm.addEventListener('submit', (e) => this.handleTaskSubmit(e));
        
        if (this.addNewTagBtn) {
            this.addNewTagBtn.addEventListener('click', () => this.addNewTag());
        }
        
        if (this.searchInput) {
            this.searchInput.addEventListener('input', () => this.searchTasks());
        }
        
        this.loadTheme();
        
        // Load the last viewed section
        this.loadLastView();
        
        updateTaskCounts();
    }

    // New method to load the last viewed section
    loadLastView() {
        const lastView = localStorage.getItem('lastView') || 'focus';
        this.showView(lastView, false); // false means don't save again
    }
    
     showView(view, saveToStorage = true) {
        [this.focusBtn, this.taskBtn, this.musicBtn].forEach(btn => {
            if (btn) btn.classList.remove('active');
        });
        
        if (view === 'focus') {
            if (this.focusBtn) this.focusBtn.classList.add('active');
            if (this.timerView) this.timerView.style.display = 'flex';
            if (this.taskView) this.taskView.style.display = 'none';
        } else if (view === 'task') {
            if (this.taskBtn) this.taskBtn.classList.add('active');
            if (this.timerView) this.timerView.style.display = 'none';
            if (this.taskView) this.taskView.style.display = 'block';
        } else if (view === 'music') {
            if (this.musicBtn) this.musicBtn.classList.add('active');
            alert('Music feature coming soon!');
            if (this.focusBtn) this.focusBtn.classList.add('active');
            if (this.timerView) this.timerView.style.display = 'flex';
            if (this.taskView) this.taskView.style.display = 'none';
            view = 'focus'; // Reset to focus since music isn't implemented
        }
        
        // Save to localStorage if needed
        if (saveToStorage) {
            localStorage.setItem('lastView', view);
        }
    }

    // Update handleTaskSubmit to stay on task view after saving
    handleTaskSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(this.taskForm);
        
        // Debug: Log form data
        console.log('Submitting form data:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        const url = formData.get('task_id') ? 'api/edit_task.php' : 'api/add_tasks.php';
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Raw response:', text);
            
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    this.closeTaskModal();
                    
                    // Make sure we stay on task view after reload
                    localStorage.setItem('lastView', 'task');
                    
                    // Reload the page
                    location.reload();
                } else {
                    alert('Error saving task: ' + (data.error || 'Unknown error'));
                }
            } catch (e) {
                console.error('JSON parse error:', e);
                alert('Server returned invalid JSON. Check console for details.');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Error saving task: ' + error.message);
        });
    }
    
    toggleTheme() {
        document.body.classList.toggle('dark');
        const icon = this.themeToggle ? this.themeToggle.querySelector('i') : null;
        if (icon) {
            if (document.body.classList.contains('dark')) {
                icon.setAttribute('data-lucide', 'sun');
                localStorage.setItem('theme', 'dark');
            } else {
                icon.setAttribute('data-lucide', 'moon');
                localStorage.setItem('theme', 'light');
            }
        }
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
    
    loadTheme() {
        const savedTheme = localStorage.getItem('theme');
        const icon = this.themeToggle ? this.themeToggle.querySelector('i') : null;
        
        if (savedTheme === 'dark') {
            document.body.classList.add('dark');
            if (icon) icon.setAttribute('data-lucide', 'sun');
        } else {
            document.body.classList.remove('dark');
            if (icon) icon.setAttribute('data-lucide', 'moon');
        }
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
    
    openTaskModal() {
        if (this.taskForm) this.taskForm.reset();
        
        const taskIdInput = document.getElementById('taskId');
        const modalTitle = document.getElementById('modalTitle');
        
        if (taskIdInput) taskIdInput.value = '';
        if (modalTitle) modalTitle.innerHTML = '<i data-lucide="plus-circle"></i> Add New Task';
        
        document.querySelectorAll('input[name="tags[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        if (this.taskModal) this.taskModal.classList.add('show');
        
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
    
    closeTaskModal() {
        if (this.taskModal) this.taskModal.classList.remove('show');
    }
    
    handleTaskSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(this.taskForm);
        const url = formData.get('task_id') ? 'tasks/edit_task.php' : 'tasks/add_task.php';
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.closeTaskModal();
                location.reload();
            } else {
                alert('Error saving task: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving task');
        });
    }
    
    addNewTag() {
        const newTagName = document.getElementById('newTagName');
        const newTagColor = document.getElementById('newTagColor');
        
        if (!newTagName || !newTagColor) return;
        
        const tagName = newTagName.value.trim();
        const tagColor = newTagColor.value;
        
        if (!tagName) {
            alert('Please enter a tag name');
            return;
        }
        
        fetch('tags/add_tag.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `name=${encodeURIComponent(tagName)}&color=${encodeURIComponent(tagColor)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tagsContainer = document.getElementById('tagsContainer');
                if (tagsContainer) {
                    const newTagHtml = `
                        <label class="tag-checkbox">
                            <input type="checkbox" name="tags[]" value="${data.tag_id}" checked>
                            <span class="tag-label" style="background-color: ${tagColor}20; color: ${tagColor}; border-color: ${tagColor}40;">
                                <i data-lucide="tag" size="12"></i>
                                ${tagName}
                            </span>
                        </label>
                    `;
                    tagsContainer.insertAdjacentHTML('beforeend', newTagHtml);
                }
                
                newTagName.value = '';
                
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            } else {
                alert('Error adding tag: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding tag');
        });
    }
    
    searchTasks() {
        if (!this.searchInput) return;
        
        const searchTerm = this.searchInput.value.toLowerCase();
        const taskCards = document.querySelectorAll('.task-card');
        
        taskCards.forEach(card => {
            const title = card.querySelector('h4')?.textContent.toLowerCase() || '';
            const description = card.querySelector('.task-description')?.textContent.toLowerCase() || '';
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
}

// ========== DRAG AND DROP ==========
function initDragAndDrop() {
    const taskCards = document.querySelectorAll('.task-card');
    const columns = document.querySelectorAll('.task-list');
    
    taskCards.forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
        card.setAttribute('draggable', 'true');
    });
    
    columns.forEach(column => {
        column.addEventListener('dragover', handleDragOver);
        column.addEventListener('dragenter', handleDragEnter);
        column.addEventListener('dragleave', handleDragLeave);
        column.addEventListener('drop', handleDrop);
    });
}

function handleDragStart(e) {
    e.target.classList.add('dragging');
    e.dataTransfer.setData('text/plain', e.target.dataset.id);
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
}

function handleDragEnter(e) {
    e.preventDefault();
    const column = e.target.closest('.task-list');
    if (column) {
        column.classList.add('drag-over');
    }
}

function handleDragLeave(e) {
    const column = e.target.closest('.task-list');
    if (column) {
        column.classList.remove('drag-over');
    }
}

function handleDrop(e) {
    e.preventDefault();
    
    const column = e.target.closest('.task-list');
    if (!column) return;
    
    column.classList.remove('drag-over');
    
    const taskId = e.dataTransfer.getData('text/plain');
    const newStatus = column.closest('.kanban-column')?.dataset.status;
    
    if (taskId && newStatus) {
        moveTask(taskId, newStatus);
    }
}

// ========== INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', function() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    
    const timer = new Timer();
    const ui = new UIController();
    
    initDragAndDrop();
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    document.addEventListener('keydown', (e) => {
        const timerView = document.getElementById('timerView');
        if (timerView && timerView.style.display !== 'none' && 
            !e.target.matches('input, textarea, select')) {
            
            if (e.code === 'Space') {
                e.preventDefault();
            }
        }
    });
});