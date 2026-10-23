<?php
// Track the current active navigation workspace
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'home';
$allowed_tabs = ['home', 'households', 'message', 'settings'];

// Fallback safety filter if a user tampers with the URL query params
if (!in_array($current_tab, $allowed_tabs)) {
    $current_tab = 'home';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dormhub - Household Management</title>
    
    <!-- Vector Icons CDN: Material Icons (Keyword 'group' displays 2-person figurine) -->
    <link href="https://googleapis.com" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f9fafb;
            --surface: #ffffff;
            --text: #1f2937;
            --text-light: #6b7280;
            --border: #e5e7eb;
            --chat-left: #f3f4f6;
            --chat-right: #e0e7ff;
        }

        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
        }

        body { 
            background-color: var(--bg); 
            color: var(--text); 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
        }

        /* Responsive Top Header Navbar */
        .navbar {
            background-color: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1.5rem;
            height: 60px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo { 
            font-size: 1.25rem; 
            font-weight: 700; 
            color: var(--primary); 
            letter-spacing: -0.025em; 
        }

        .nav-tabs { 
            display: flex; 
            gap: 0.5rem; 
            height: 100%; 
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0 1rem;
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease;
        }

        .nav-item:hover { 
            color: var(--text); 
            background-color: #f3f4f6; 
        }
        
        .nav-item.active { 
            color: var(--primary); 
            border-bottom-color: var(--primary); 
        }

        /* Workspace Presentation Card Dimensions */
        .main-container { 
            flex: 1; 
            max-width: 1200px; 
            width: 100%; 
            margin: 0 auto; 
            padding: 2rem 1.5rem; 
        }
        
        .card { 
            background: var(--surface); 
            border: 1px solid var(--border); 
            border-radius: 12px; 
            padding: 2rem; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.05); 
            margin-bottom: 1.5rem;
        }
        
        h1 { 
            font-size: 1.75rem; 
            margin-bottom: 0.5rem; 
            font-weight: 800; 
            letter-spacing: -0.02em; 
        }
        
        p { 
            color: var(--text-light); 
            line-height: 1.5; 
            margin-bottom: 1.5rem;
        }

        /* Standardized Form Input Layouts */
        .form-group {
            margin-bottom: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text);
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            max-width: 400px;
            padding: 0.65rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.95rem;
            outline: none;
            transition: border 0.15s ease;
        }

        input:focus {
            border-color: var(--primary);
        }

        .btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.65rem 1.25rem;
            font-weight: 500;
            font-size: 0.95rem;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .btn:hover {
            background-color: var(--primary-hover);
        }

        /* Messaging Layout Windows */
        .chat-container {
            border: 1px solid var(--border);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            height: 350px;
            background-color: #fafafa;
        }

        .chat-messages {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .message-bubble {
            max-width: 70%;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            font-size: 0.925rem;
            line-height: 1.4;
        }

        .message-bubble.incoming {
            background-color: var(--chat-left);
            align-self: flex-start;
        }

        .message-bubble.outgoing {
            background-color: var(--chat-right);
            align-self: flex-end;
            color: #1e1b4b;
        }

        .chat-meta {
            font-size: 0.75rem;
            color: var(--text-light);
            margin-bottom: 0.25rem;
            display: block;
        }

        .chat-input-bar {
            padding: 0.75rem;
            border-top: 1px solid var(--border);
            background-color: var(--surface);
            display: flex;
            gap: 0.5rem;
        }

        .chat-input-bar input {
            flex: 1;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            outline: none;
        }

        /* Mobile Breakpoint Optimization */
        @media (max-width: 640px) {
            .navbar { flex-direction: column; height: auto; padding: 0; }
            .logo { padding: 1rem 0 0.5rem 0; width: 100%; text-align: center; }
            .nav-tabs { width: 100%; justify-content: space-around; border-top: 1px solid var(--border); }
            .nav-item { padding: 0.75rem 0; flex: 1; flex-direction: column; gap: 0.1rem; font-size: 0.75rem; border-bottom: none; border-top: 3px solid transparent; }
            .nav-item.active { border-bottom-color: transparent; border-top-color: var(--primary); }
            .main-container { padding: 1rem; }
            input[type="text"], input[type="email"] { max-width: 100%; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">Dormhub</div>
        <div class="nav-tabs">
            <a href="?tab=home" class="nav-item <?php echo $current_tab === 'home' ? 'active' : ''; ?>">
                <span class="material-icons">home</span>Home
            </a>
            <!-- Households Tab with the 'group' 2-person figurine icon -->
            <a href="?tab=households" class="nav-item <?php echo $current_tab === 'households' ? 'active' : ''; ?>">
                <span class="material-icons">group</span>Households
            </a>
            <a href="?tab=message" class="nav-item <?php echo $current_tab === 'message' ? 'active' : ''; ?>">
                <span class="material-icons">chat</span>Message
            </a>
            <a href="?tab=settings" class="nav-item <?php echo $current_tab === 'settings' ? 'active' : ''; ?>">
                <span class="material-icons">settings</span>Settings
            </a>
        </div>
    </nav>

    <!-- Dynamic Context Container Switching Elements -->
    <main class="main-container">
        
        <!-- HOME WORKSPACE PANELS -->
        <?php if ($current_tab === 'home'): ?>
            <div class="card">
                <h1>Welcome to Your Household</h1>
                <p>Track your shared chores, expenses, shopping lists, and calendar events right here from your main dashboard view.</p>
            </div>

        <!-- HOUSEHOLDS WORKSPACE PANELS -->
        <?php elseif ($current_tab === 'households'): ?>
            <div class="card">
                <h1>Create a Household</h1>
                <p>Set up a brand new shared workspace group configuration for your dorm room or apartment building layout.</p>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="hh_name">Household Name</label>
                        <input type="text" id="hh_name" name="household_name" placeholder="e.g., Suite 404, Taylor Hall">
                    </div>
                    <button type="submit" class="btn">Create Group</button>
                </form>
            </div>

            <div class="card">
                <h1>Invite Roommates</h1>
                <p>Generate invite tokens to add members to your active living space directory.</p>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="rm_email">Roommate Email Address</label>
                        <input type="email" id="rm_email" name="roommate_email" placeholder="roommate@university.edu">
                    </div>
                    <button type="submit" class="btn">Send Invite Link</button>
                </form>
            </div>

        <!-- MESSAGE WORKSPACE PANELS -->
        <?php elseif ($current_tab === 'message'): ?>
            <div class="card">
                <h1>Household Messages</h1>
