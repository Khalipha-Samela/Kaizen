<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaizen</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Load Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!--icon-->
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <style>
        /* Fullscreen timer styles */
        .timer-container.fullscreen-mode {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: var(--bg-primary);
            z-index: 9999;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* MUCH BIGGER TIMER IN FULLSCREEN */
        .timer-container.fullscreen-mode #timer {
            font-size: min(30vw, 30vh, 500px) !important;
            margin-bottom: 0;
            transition: all 0.3s ease;
            line-height: 1;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        /* Even bigger on very large screens */
        @media (min-width: 1200px) {
            .timer-container.fullscreen-mode #timer {
                font-size: min(35vw, 35vh, 600px) !important;
            }
        }

        /* Force hide all controls and hints by default in fullscreen */
        .timer-container.fullscreen-mode .timer-controls,
        .timer-container.fullscreen-mode .fullscreen-mode-indicator,
        .timer-container.fullscreen-mode .fullscreen-exit-hint {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        /* Show controls and hints on hover - with !important to override */
        .timer-container.fullscreen-mode:hover .timer-controls {
            display: flex !important;
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: all !important;
        }

        .timer-container.fullscreen-mode:hover .fullscreen-exit-hint {
            display: flex !important;
            opacity: 0.7 !important;
            visibility: visible !important;
            pointer-events: none !important;
        }

        .timer-container.fullscreen-mode:hover .fullscreen-mode-indicator {
            display: flex !important;
            opacity: 0.5 !important;
            visibility: visible !important;
            pointer-events: none !important;
        }

        /* Slightly dim the timer when showing controls */
        .timer-container.fullscreen-mode:hover #timer {
            opacity: 0.9;
        }

        .timer-container.fullscreen-mode .navbar,
        .timer-container.fullscreen-mode .footer,
        .timer-container.fullscreen-mode #taskView {
            display: none !important;
        }

        /* Fullscreen exit hint */
        .fullscreen-exit-hint {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 14px;
            backdrop-filter: blur(5px);
            z-index: 10000;
            display: none;
            align-items: center;
            gap: 8px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease;
            pointer-events: none;
        }

        .fullscreen-exit-hint i {
            width: 16px;
            height: 16px;
        }

        /* Fullscreen mode indicator */
        .fullscreen-mode-indicator {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.4);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            backdrop-filter: blur(5px);
            z-index: 10000;
            display: none;
            align-items: center;
            gap: 6px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease;
            pointer-events: none;
        }

        .fullscreen-mode-indicator i {
            width: 12px;
            height: 12px;
        }

        /* Ensure controls are properly centered in fullscreen */
        .timer-container.fullscreen-mode .timer-controls {
            position: absolute;
            bottom: 10%;
            left: 50%;
            transform: translateX(-50%);
            width: auto;
            max-width: 90%;
            z-index: 10001;
        }

        /* Make buttons slightly larger in fullscreen */
        .timer-container.fullscreen-mode .timer-btn {
            transform: scale(1.2);
            margin: 0 6px;
            font-size: 1rem;
            padding: var(--space-3) var(--space-5);
        }

        /* Adjust control groups for fullscreen */
        .timer-container.fullscreen-mode .initial-controls,
        .timer-container.fullscreen-mode .active-controls,
        .timer-container.fullscreen-mode .paused-controls {
            gap: 16px;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">
            <div class="logo-icon"></div>
        </div>

        <div class="menu">
            <button class="active" id="focusBtn">
                <i data-lucide="clock"></i>
                <span>FOCUS</span>
            </button>
            <button id="taskBtn">
                <i data-lucide="check-square"></i>
                <span>TASK</span>
            </button>
            <button id="musicBtn">
                <i data-lucide="music"></i>
                <span>MUSIC</span>
            </button>
        </div>

        <div class="theme-toggle" id="themeToggle">
            <i data-lucide="moon"></i>
        </div>
    </div>

    <div class="timer-container" id="timerView">
        <!-- Fullscreen mode indicator (only visible on hover in fullscreen) -->
        <div class="fullscreen-mode-indicator" id="fullscreenIndicator">
            <i data-lucide="maximize" size="12"></i>
            <span>Fullscreen Mode</span>
        </div>

        <h1 id="timer">00:00</h1>

        <div class="timer-controls">
            <!-- Initial state - only START visible -->
            <div class="buttons initial-controls" id="initialControls">
                <button id="startBtn" class="timer-btn primary">
                    <i data-lucide="play"></i>
                    <span>START</span>
                </button>
                <button id="settingsBtn" class="timer-btn secondary">
                    <i data-lucide="settings"></i>
                    <span>SETTINGS</span>
                </button>
                <button id="fullscreenBtn" class="timer-btn icon-only" title="Enter Fullscreen">
                    <i data-lucide="maximize"></i>
                </button>
            </div>

            <!-- Active state - shown when timer is running -->
            <div class="buttons active-controls" id="activeControls" style="display: none;">
                <div class="control-group primary-group">
                    <button id="pauseBtn" class="timer-btn primary">
                        <i data-lucide="pause"></i>
                        <span>PAUSE</span>
                    </button>
                </div>
                
                <div class="control-group secondary-group">
                    <button id="stopBtn" class="timer-btn secondary">
                        <i data-lucide="square"></i>
                        <span>STOP</span>
                    </button>
                    <button id="resetBtn" class="timer-btn secondary">
                        <i data-lucide="rotate-ccw"></i>
                        <span>RESET</span>
                    </button>
                    <button id="activeSettingsBtn" class="timer-btn secondary">
                        <i data-lucide="settings"></i>
                        <span>SETTINGS</span>
                    </button>
                    <button id="activeFullscreenBtn" class="timer-btn icon-only" title="Enter Fullscreen">
                        <i data-lucide="maximize"></i>
                    </button>
                </div>
            </div>

            <!-- Paused state - shown when timer is paused -->
            <div class="buttons paused-controls" id="pausedControls" style="display: none;">
                <div class="control-group primary-group">
                    <button id="pausedResumeBtn" class="timer-btn primary">
                        <i data-lucide="play"></i>
                        <span>RESUME</span>
                    </button>
                </div>
                
                <div class="control-group secondary-group">
                    <button id="pausedStopBtn" class="timer-btn secondary">
                        <i data-lucide="square"></i>
                        <span>STOP</span>
                    </button>
                    <button id="pausedResetBtn" class="timer-btn secondary">
                        <i data-lucide="rotate-ccw"></i>
                        <span>RESET</span>
                    </button>
                    <button id="pausedSettingsBtn" class="timer-btn secondary">
                        <i data-lucide="settings"></i>
                        <span>SETTINGS</span>
                    </button>
                    <button id="pausedFullscreenBtn" class="timer-btn icon-only" title="Enter Fullscreen">
                        <i data-lucide="maximize"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Timer Settings Modal -->
        <div id="timerSettingsModal" class="modal small">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>
                        <i data-lucide="clock"></i>
                        Timer Settings
                    </h3>
                    <button class="close-modal" id="closeTimerSettingsBtn">
                        <i data-lucide="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            <i data-lucide="settings"></i>
                            Timer Mode
                        </label>
                        <select id="timerMode" class="timer-select">
                            <option value="indefinite">Indefinite (Counts up from 00:00)</option>
                            <option value="pomodoro">Pomodoro (Structured Sessions)</option>
                        </select>
                    </div>
                    
                    <!-- Pomodoro Settings -->
                    <div id="pomodoroSettings" style="display: none;">
                        <div class="form-group">
                            <label>
                                <i data-lucide="clock"></i>
                                Focus Duration (minutes)
                            </label>
                            <input type="number" id="focusDuration" min="1" max="120" value="25" class="timer-input">
                        </div>
                        <div class="form-group">
                            <label>
                                <i data-lucide="coffee"></i>
                                Break Duration (minutes)
                            </label>
                            <input type="number" id="breakDuration" min="1" max="30" value="5" class="timer-input">
                        </div>
                        <div class="form-group">
                            <label>
                                <i data-lucide="repeat"></i>
                                Auto-start breaks
                            </label>
                            <label class="toggle-switch">
                                <input type="checkbox" id="autoStartBreaks" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Indefinite Settings -->
                    <div id="indefiniteSettings" style="display: block;">
                        <p class="text-secondary" style="margin-bottom: var(--space-3); display: flex; align-items: center; gap: var(--space-2); padding: var(--space-3); background: var(--bg-secondary); border-radius: var(--radius); border: 1px solid var(--border);">
                            <i data-lucide="info" size="16" style="color: var(--primary); flex-shrink: 0;"></i>
                            Indefinite mode counts up from 00:00. Perfect for general timing needs.
                        </p>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i data-lucide="bell"></i>
                            Sound Alert
                        </label>
                        <select id="timerSound" class="timer-select">
                            <option value="none">None</option>
                            <option value="bell" selected>Bell</option>
                            <option value="digital">Digital</option>
                            <option value="gentle">Gentle</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="cancel-btn" id="cancelTimerSettingsBtn">Cancel</button>
                    <button class="save-btn" id="saveTimerSettingsBtn">Save Settings</button>
                </div>
            </div>
        </div>
    </div>

    <div id="taskView" style="display: none;">
        <div class="task-header">
            <h2>
                <i data-lucide="layout-dashboard"></i>
                Task Board
            </h2>
            <button id="openAddModalBtn" class="add-task-btn">
                <i data-lucide="plus"></i>
                ADD NEW TASK
            </button>
        </div>

        <!-- Search input -->
        <input type="text" id="searchTasks" class="search-input" placeholder="Search tasks...">

        <div class="kanban-board">
            <!-- Todo Column -->
            <div class="kanban-column" data-status="todo">
                <div class="column-header">
                    <h3>
                        <i data-lucide="circle"></i>
                        To Do
                    </h3>
                    <span class="task-count" id="todo-count">0</span>
                </div>
                <div class="task-list" id="todo-tasks">
                    <?php
                    require_once "config/db.php";
                    
                    if (!$conn) {
                        echo '<div class="error">Database connection failed</div>';
                    } else {
                        $stmt = $conn->prepare("
                            SELECT t.*, 
                                   GROUP_CONCAT(CONCAT(tg.id, ':', tg.name, ':', tg.color) SEPARATOR '|') as tags
                            FROM tasks t
                            LEFT JOIN task_tags tt ON t.id = tt.task_id
                            LEFT JOIN tags tg ON tt.tag_id = tg.id
                            WHERE t.status = 'todo'
                            GROUP BY t.id
                            ORDER BY FIELD(priority, 'urgent', 'high', 'medium', 'low'), t.created_at DESC
                        ");
                        
                        if ($stmt) {
                            $stmt->execute();
                            $todoTasks = $stmt->get_result();
                            
                            if ($todoTasks->num_rows === 0) {
                                echo '<div class="empty-state">No tasks in To Do</div>';
                            } else {
                                while ($row = $todoTasks->fetch_assoc()):
                                    $priorityClass = $row['priority'];
                                    $dueDate = $row['due_date'] ? date('M j', strtotime($row['due_date'])) : null;
                                    $isOverdue = $row['due_date'] && strtotime($row['due_date']) < strtotime('today');
                                ?>
                                    <div class="task-card" data-id="<?php echo $row['id']; ?>" data-priority="<?php echo $row['priority']; ?>">
                                        <div class="task-card-header">
                                            <h4><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                            <span class="priority-badge <?php echo $priorityClass; ?>">
                                                <?php 
                                                $priorityIcons = [
                                                    'low' => 'arrow-down',
                                                    'medium' => 'minus',
                                                    'high' => 'arrow-up',
                                                    'urgent' => 'alert-circle'
                                                ];
                                                ?>
                                                <i data-lucide="<?php echo $priorityIcons[$priorityClass]; ?>" size="12"></i>
                                                <?php echo ucfirst($row['priority']); ?>
                                            </span>
                                        </div>
                                        
                                        <?php if ($row['description']): ?>
                                            <p class="task-description">
                                                <i data-lucide="file-text" size="14"></i>
                                                <?php echo htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : ''); ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if ($row['tags']): ?>
                                            <div class="task-tags">
                                                <?php 
                                                $tags = explode('|', $row['tags']);
                                                foreach ($tags as $tag):
                                                    list($tagId, $tagName, $tagColor) = explode(':', $tag);
                                                ?>
                                                    <span class="tag" style="background-color: <?php echo $tagColor; ?>20; color: <?php echo $tagColor; ?>; border-color: <?php echo $tagColor; ?>40;">
                                                        <i data-lucide="tag" size="12"></i>
                                                        <?php echo htmlspecialchars($tagName); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="task-card-footer">
                                            <?php if ($dueDate): ?>
                                                <span class="due-date <?php echo $isOverdue ? 'overdue' : ''; ?>">
                                                    <i data-lucide="calendar"></i>
                                                    <?php echo $dueDate; ?>
                                                    <?php if ($isOverdue): ?>
                                                        <span class="overdue-badge">
                                                            <i data-lucide="alert-triangle" size="12"></i>
                                                            Overdue
                                                        </span>
                                                    <?php endif; ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <div class="task-card-actions">
                                                <button class="icon-btn edit-btn" onclick="editTask(<?php echo $row['id']; ?>)">
                                                    <i data-lucide="edit"></i>
                                                </button>
                                                <button class="icon-btn move-btn" onclick="moveTask(<?php echo $row['id']; ?>, 'progress')">
                                                    <i data-lucide="arrow-right"></i>
                                                </button>
                                                <button class="icon-btn delete-btn" onclick="deleteTask(<?php echo $row['id']; ?>)">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                                endwhile;
                            }
                            $stmt->close();
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- In Progress Column -->
            <div class="kanban-column" data-status="progress">
                <div class="column-header">
                    <h3>
                        <i data-lucide="loader"></i>
                        In Progress
                    </h3>
                    <span class="task-count" id="progress-count">0</span>
                </div>
                <div class="task-list" id="progress-tasks">
                    <?php
                    if ($conn) {
                        $stmt = $conn->prepare("
                            SELECT t.*, 
                                   GROUP_CONCAT(CONCAT(tg.id, ':', tg.name, ':', tg.color) SEPARATOR '|') as tags
                            FROM tasks t
                            LEFT JOIN task_tags tt ON t.id = tt.task_id
                            LEFT JOIN tags tg ON tt.tag_id = tg.id
                            WHERE t.status = 'progress'
                            GROUP BY t.id
                            ORDER BY FIELD(priority, 'urgent', 'high', 'medium', 'low'), t.created_at DESC
                        ");
                        
                        if ($stmt) {
                            $stmt->execute();
                            $progressTasks = $stmt->get_result();
                            
                            if ($progressTasks->num_rows === 0) {
                                echo '<div class="empty-state">No tasks in Progress</div>';
                            } else {
                                while ($row = $progressTasks->fetch_assoc()): 
                                    $priorityClass = $row['priority'];
                                    $dueDate = $row['due_date'] ? date('M j', strtotime($row['due_date'])) : null;
                                    $isOverdue = $row['due_date'] && strtotime($row['due_date']) < strtotime('today');
                                ?>
                                    <div class="task-card" data-id="<?php echo $row['id']; ?>" data-priority="<?php echo $row['priority']; ?>">
                                        <div class="task-card-header">
                                            <h4><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                            <span class="priority-badge <?php echo $priorityClass; ?>">
                                                <?php 
                                                $priorityIcons = [
                                                    'low' => 'arrow-down',
                                                    'medium' => 'minus',
                                                    'high' => 'arrow-up',
                                                    'urgent' => 'alert-circle'
                                                ];
                                                ?>
                                                <i data-lucide="<?php echo $priorityIcons[$priorityClass]; ?>" size="12"></i>
                                                <?php echo ucfirst($row['priority']); ?>
                                            </span>
                                        </div>
                                        
                                        <?php if ($row['description']): ?>
                                            <p class="task-description">
                                                <i data-lucide="file-text" size="14"></i>
                                                <?php echo htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : ''); ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if ($row['tags']): ?>
                                            <div class="task-tags">
                                                <?php 
                                                $tags = explode('|', $row['tags']);
                                                foreach ($tags as $tag):
                                                    list($tagId, $tagName, $tagColor) = explode(':', $tag);
                                                ?>
                                                    <span class="tag" style="background-color: <?php echo $tagColor; ?>20; color: <?php echo $tagColor; ?>; border-color: <?php echo $tagColor; ?>40;">
                                                        <i data-lucide="tag" size="12"></i>
                                                        <?php echo htmlspecialchars($tagName); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="task-card-footer">
                                            <?php if ($dueDate): ?>
                                                <span class="due-date <?php echo $isOverdue ? 'overdue' : ''; ?>">
                                                    <i data-lucide="calendar"></i>
                                                    <?php echo $dueDate; ?>
                                                    <?php if ($isOverdue): ?>
                                                        <span class="overdue-badge">
                                                            <i data-lucide="alert-triangle" size="12"></i>
                                                            Overdue
                                                        </span>
                                                    <?php endif; ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <div class="task-card-actions">
                                                <button class="icon-btn edit-btn" onclick="editTask(<?php echo $row['id']; ?>)">
                                                    <i data-lucide="edit"></i>
                                                </button>
                                                <button class="icon-btn move-btn" onclick="moveTask(<?php echo $row['id']; ?>, 'todo')">
                                                    <i data-lucide="arrow-left"></i>
                                                </button>
                                                <button class="icon-btn move-btn" onclick="moveTask(<?php echo $row['id']; ?>, 'done')">
                                                    <i data-lucide="arrow-right"></i>
                                                </button>
                                                <button class="icon-btn delete-btn" onclick="deleteTask(<?php echo $row['id']; ?>)">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                                endwhile;
                            }
                            $stmt->close();
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- Done Column -->
            <div class="kanban-column" data-status="done">
                <div class="column-header">
                    <h3>
                        <i data-lucide="check-circle"></i>
                        Done
                    </h3>
                    <span class="task-count" id="done-count">0</span>
                </div>
                <div class="task-list" id="done-tasks">
                    <?php
                    if ($conn) {
                        $stmt = $conn->prepare("
                            SELECT t.*, 
                                   GROUP_CONCAT(CONCAT(tg.id, ':', tg.name, ':', tg.color) SEPARATOR '|') as tags
                            FROM tasks t
                            LEFT JOIN task_tags tt ON t.id = tt.task_id
                            LEFT JOIN tags tg ON tt.tag_id = tg.id
                            WHERE t.status = 'done'
                            GROUP BY t.id
                            ORDER BY t.created_at DESC
                        ");
                        
                        if ($stmt) {
                            $stmt->execute();
                            $doneTasks = $stmt->get_result();
                            
                            if ($doneTasks->num_rows === 0) {
                                echo '<div class="empty-state">No tasks in Done</div>';
                            } else {
                                while ($row = $doneTasks->fetch_assoc()): 
                                    $priorityClass = $row['priority'];
                                ?>
                                    <div class="task-card done" data-id="<?php echo $row['id']; ?>">
                                        <div class="task-card-header">
                                            <h4><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                            <span class="priority-badge <?php echo $priorityClass; ?>">
                                                <?php 
                                                $priorityIcons = [
                                                    'low' => 'arrow-down',
                                                    'medium' => 'minus',
                                                    'high' => 'arrow-up',
                                                    'urgent' => 'alert-circle'
                                                ];
                                                ?>
                                                <i data-lucide="<?php echo $priorityIcons[$priorityClass]; ?>" size="12"></i>
                                                <?php echo ucfirst($row['priority']); ?>
                                            </span>
                                        </div>
                                        
                                        <?php if ($row['description']): ?>
                                            <p class="task-description">
                                                <i data-lucide="file-text" size="14"></i>
                                                <?php echo htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : ''); ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if ($row['tags']): ?>
                                            <div class="task-tags">
                                                <?php 
                                                $tags = explode('|', $row['tags']);
                                                foreach ($tags as $tag):
                                                    list($tagId, $tagName, $tagColor) = explode(':', $tag);
                                                ?>
                                                    <span class="tag" style="background-color: <?php echo $tagColor; ?>20; color: <?php echo $tagColor; ?>; border-color: <?php echo $tagColor; ?>40;">
                                                        <i data-lucide="tag" size="12"></i>
                                                        <?php echo htmlspecialchars($tagName); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="task-card-footer">
                                            <div class="task-card-actions">
                                                <button class="icon-btn edit-btn" onclick="editTask(<?php echo $row['id']; ?>)">
                                                    <i data-lucide="edit"></i>
                                                </button>
                                                <button class="icon-btn move-btn" onclick="moveTask(<?php echo $row['id']; ?>, 'progress')">
                                                    <i data-lucide="arrow-left"></i>
                                                </button>
                                                <button class="icon-btn delete-btn" onclick="deleteTask(<?php echo $row['id']; ?>)">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                                endwhile;
                            }
                            $stmt->close();
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Modal (Add/Edit) -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">
                    <i data-lucide="plus-circle"></i>
                    Add New Task
                </h3>
                <button class="close-modal" id="closeModalBtn">
                    <i data-lucide="x"></i>
                </button>
            </div>
            
            <form id="taskForm" method="POST">
                <input type="hidden" name="task_id" id="taskId">
                
                <div class="form-group">
                    <label for="taskTitle">
                        <i data-lucide="heading" size="16"></i>
                        Title *
                    </label>
                    <input type="text" id="taskTitle" name="title" required maxlength="255" placeholder="Enter task title">
                </div>
                
                <div class="form-group">
                    <label for="taskDescription">
                        <i data-lucide="file-text" size="16"></i>
                        Description
                    </label>
                    <textarea id="taskDescription" name="description" rows="3" placeholder="Enter task description"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="taskPriority">
                            <i data-lucide="flag" size="16"></i>
                            Priority
                        </label>
                        <select id="taskPriority" name="priority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="taskDueDate">
                            <i data-lucide="calendar" size="16"></i>
                            Due Date
                        </label>
                        <input type="date" id="taskDueDate" name="due_date">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>
                        <i data-lucide="tags" size="16"></i>
                        Tags
                    </label>
                    <div class="tags-container" id="tagsContainer">
                        <?php
                        if ($conn) {
                            $tagResult = $conn->query("SELECT * FROM tags ORDER BY name");
                            while ($tag = $tagResult->fetch_assoc()):
                            ?>
                            <label class="tag-checkbox">
                                <input type="checkbox" name="tags[]" value="<?php echo $tag['id']; ?>">
                                <span class="tag-label" style="background-color: <?php echo $tag['color']; ?>20; color: <?php echo $tag['color']; ?>; border-color: <?php echo $tag['color']; ?>40;">
                                    <i data-lucide="tag" size="12"></i>
                                    <?php echo htmlspecialchars($tag['name']); ?>
                                </span>
                            </label>
                            <?php 
                            endwhile;
                        }
                        ?>
                    </div>

                    <!-- Add new tag section -->
                    <div class="new-tag-section">
                        <div class="new-tag-input-group">
                            <input type="text" id="newTagName" placeholder="New tag name..." maxlength="30">
                            <input type="color" id="newTagColor" value="#6366f1">
                            <button type="button" id="addNewTagBtn" class="add-tag-btn">
                                <i data-lucide="plus"></i> Add Tag
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="taskStatus">
                        <i data-lucide="git-branch" size="16"></i>
                        Status
                    </label>
                    <select id="taskStatus" name="status">
                        <option value="todo">To Do</option>
                        <option value="progress">In Progress</option>
                        <option value="done">Done</option>
                    </select>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="cancel-btn" id="cancelModalBtn">
                        <i data-lucide="x"></i>
                        Cancel
                    </button>
                    <button type="submit" class="save-btn">
                        <i data-lucide="save"></i>
                        Save Task
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="footer">
        <i data-lucide="heart" size="14" class="footer-heart"></i>
        © <?php echo date("Y"); ?> Kaizen — MADE BY Khalipha Samela
    </div>

    <!-- Fullscreen exit hint (only visible on hover in fullscreen) -->
    <div class="fullscreen-exit-hint" id="fullscreenExitHint">
        <i data-lucide="x"></i>
        <span>Press ESC to exit fullscreen</span>
    </div>

    <!-- Load script after everything -->
    <script src="assets/js/script.js"></script>
    <script>
        // Initialize Lucide icons after page load
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            const timerContainer = document.querySelector('.timer-container');
            const fullscreenButtons = document.querySelectorAll('#fullscreenBtn, #activeFullscreenBtn, #pausedFullscreenBtn');
            const fullscreenIndicator = document.getElementById('fullscreenIndicator');
            const exitHint = document.getElementById('fullscreenExitHint');
            
            // Fullscreen change handler
            function handleFullscreenChange() {
                if (document.fullscreenElement) {
                    // Entered fullscreen
                    timerContainer.classList.add('fullscreen-mode');
                    
                    // Update icon for fullscreen buttons
                    fullscreenButtons.forEach(btn => {
                        const icon = btn.querySelector('i');
                        if (icon) {
                            icon.setAttribute('data-lucide', 'minimize');
                        }
                        btn.setAttribute('title', 'Exit Fullscreen');
                    });
                    
                    // Request landscape orientation on mobile
                    if (screen.orientation && screen.orientation.lock) {
                        screen.orientation.lock('landscape').catch(() => {});
                    }
                } else {
                    // Exited fullscreen
                    timerContainer.classList.remove('fullscreen-mode');
                    
                    // Update icon for fullscreen buttons
                    fullscreenButtons.forEach(btn => {
                        const icon = btn.querySelector('i');
                        if (icon) {
                            icon.setAttribute('data-lucide', 'maximize');
                        }
                        btn.setAttribute('title', 'Enter Fullscreen');
                    });
                    
                    // Unlock orientation
                    if (screen.orientation && screen.orientation.unlock) {
                        screen.orientation.unlock();
                    }
                }
                
                // Recreate icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
            
            // Add fullscreen change listeners
            document.addEventListener('fullscreenchange', handleFullscreenChange);
            document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
            document.addEventListener('mozfullscreenchange', handleFullscreenChange);
            document.addEventListener('MSFullscreenChange', handleFullscreenChange);
            
            // Add click handlers to fullscreen buttons
            fullscreenButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (!document.fullscreenElement) {
                        // Enter fullscreen on the timer container
                        if (timerContainer.requestFullscreen) {
                            timerContainer.requestFullscreen();
                        } else if (timerContainer.webkitRequestFullscreen) {
                            timerContainer.webkitRequestFullscreen();
                        } else if (timerContainer.mozRequestFullScreen) {
                            timerContainer.mozRequestFullScreen();
                        } else if (timerContainer.msRequestFullscreen) {
                            timerContainer.msRequestFullscreen();
                        }
                    } else {
                        // Exit fullscreen
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        } else if (document.webkitExitFullscreen) {
                            document.webkitExitFullscreen();
                        } else if (document.mozCancelFullScreen) {
                            document.mozCancelFullScreen();
                        } else if (document.msExitFullscreen) {
                            document.msExitFullscreen();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>