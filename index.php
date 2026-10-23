<?php
session_start();
/* line 2 starts a session so that the users activities are preserved, you can exit
 you can exit the page come back and you session will still be there.
*/
// ============================================================
// 1. LOGIN PROTECTION
// ============================================================
// If the user is not logged in, send them to login.php.

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);


// ============================================================
// 2. ACTIVE TAB / NAVIGATION
// ============================================================

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'home';
/* lines 26 to 30 creates an array of tabs that are currenttly
allowed in this current development state
*/
$allowed = [
    'home',
    'households',
    'message',
    'settings'
];

if (!in_array($current_tab, $allowed)) {
    $current_tab = 'home';
}
/*
Lines 31 to 32 make sure that if the user is not on an allowed tab 
then they are defaulted to the home page tab.
*/


/*As of right now the messages on the message board are stored on a text file called
messages.txt in the same folder as the index.php file so that as 
users message on the board it will be stored in the file. 
the DIR constant direccts the msg_file to the messages.txt */

$msg_file = __DIR__ . '/messages.txt';


// ============================================================
// 4. HANDLE NEW MESSAGE
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['send_message'])) {

    // Get message from form
    $user_msg = trim($_POST['message_content'] ?? '');

    // Only save if message is not empty
    if ($user_msg !== '') {

        // Get current time
        $timestamp = date('h:i A');

        // Get logged-in user's name
        $username = htmlspecialchars(
            $_SESSION["user_name"],
            ENT_QUOTES,
            'UTF-8'
        );

        // Protect the message from HTML injection
        $message = htmlspecialchars(
            $user_msg,
            ENT_QUOTES,
            'UTF-8'
        );

        // Create the formatted message
        $formatted_msg =
            "<strong>{$username}</strong> • " .
            "<span style='color:#6b7280;'>{$timestamp}</span>" .
            "<br>" .
            "{$message}" .
            "\n---\n";

        // Save message
        $result = file_put_contents(
            $msg_file,
            $formatted_msg,
            FILE_APPEND | LOCK_EX
        );

        // If saving failed, show an error
        if ($result === false) {
            die(
                "ERROR: PHP could not write to messages.txt. " .
                "Make sure the file is in the same folder."
            );
        }
        // the above code will change when using a mesaage board that stores messages
        // on a different source so they accesible to other users not just locally


        // brings us back to the message page by defualt.
        header("Location: index.php?tab=message");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dormhub</title>

    <!-- =====================================================
         GOOGLE MATERIAL ICONS
    ====================================================== -->

    <link
        href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet"
    >

    <!-- =====================================================
         CSS
    ====================================================== -->

    <style>

        /* =====================================================
           PAGE
        ====================================================== */

        body {
            font-family: Arial, sans-serif;
            background: #f9fafb;
            margin: 0;
            padding: 20px;
            color: #1f2937;
        }


        /* =====================================================
           NAVIGATION
        ====================================================== */

        .navbar {
            background: white;
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;

            display: flex;
            align-items: center;
            gap: 20px;

            border-radius: 8px;
        }

        .brand {
            color: #4f46e5;
            font-size: 20px;
            font-weight: bold;
            margin-right: 10px;
        }

        .nav-item {
            display: flex;
            align-items: center;

            gap: 5px;

            color: #6b7280;

            text-decoration: none;

            font-weight: bold;

            padding: 8px 5px;
        }

        .nav-item:hover {
            color: #4f46e5;
        }

        .nav-item.active {
            color: #4f46e5;
        }


        /* =====================================================
           USER AREA
        ====================================================== */

        .user-area {
            margin-left: auto;

            display: flex;
            align-items: center;

            gap: 15px;

            font-size: 14px;
        }

        .logout {
            color: #dc2626;
            text-decoration: none;
            font-weight: bold;
        }

        .logout:hover {
            text-decoration: underline;
        }


        /* =====================================================
           MAIN CARD
        ====================================================== */

        .card {
            background: white;

            padding: 30px;

            border-radius: 8px;

            margin-top: 20px;

            border: 1px solid #e5e7eb;
        }


        /* =====================================================
           HEADINGS
        ====================================================== */

        h1 {
            margin-top: 0;
            color: #111827;
        }


        /* =====================================================
           FORMS
        ====================================================== */

        .form-group {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="email"] {

            padding: 10px;

            border: 1px solid #ccc;

            border-radius: 6px;

            width: 280px;

            font-size: 14px;

            box-sizing: border-box;
        }


        /* =====================================================
           BUTTONS
        ====================================================== */

        .btn {

            background: #4f46e5;

            color: white;

            border: none;

            padding: 10px 20px;

            border-radius: 6px;

            cursor: pointer;

            font-weight: bold;
        }

        .btn:hover {
            background: #4338ca;
        }


        /* =====================================================
           CHAT CONTAINER
        ====================================================== */

        .chat-container {

            border: 1px solid #e5e7eb;

            border-radius: 8px;

            background: #fafafa;

            display: flex;

            flex-direction: column;

            max-width: 600px;

            overflow: hidden;
        }


        /* =====================================================
           CHAT MESSAGES
        ====================================================== */

        .chat-messages {

            padding: 15px;

            height: 300px;

            overflow-y: auto;

            display: flex;

            flex-direction: column;

            gap: 12px;
        }


        /* =====================================================
           CHAT BUBBLE
        ====================================================== */

        .chat-bubble {

            background: #e0e7ff;

            color: #1e1b4b;

            padding: 10px 14px;

            border-radius: 8px;

            line-height: 1.4;

            font-size: 14px;

            align-self: flex-start;

            max-width: 85%;

            word-wrap: break-word;
        }


        /* =====================================================
           CHAT INPUT
        ====================================================== */

        .chat-input-bar {

            display: flex;

            gap: 10px;

            padding: 12px;

            border-top: 1px solid #e5e7eb;

            background: white;
        }

        .chat-input-bar input {

            flex: 1;

            width: auto;
        }


        /* =====================================================
           SETTINGS
        ====================================================== */

        .settings-box {

            max-width: 500px;

            background: #f9fafb;

            border: 1px solid #e5e7eb;

            border-radius: 8px;

            padding: 20px;
        }

        .settings-row {

            margin-bottom: 15px;

        }

        .settings-label {

            font-weight: bold;

            display: block;

            margin-bottom: 5px;

        }


        /* =====================================================
           EMPTY MESSAGE
        ====================================================== */

        .no-messages {

            color: #6b7280;

            font-style: italic;

        }

    </style>

</head>


<body>


<!-- =========================================================
     NAVIGATION BAR
========================================================= -->

<nav class="navbar">


    <!-- BRAND -->

    <div class="brand">
        Dormhub
    </div>


    <!-- HOME -->

    <a
        href="index.php?tab=home"
        class="nav-item <?php
            echo ($current_tab === 'home')
                ? 'active'
                : '';
        ?>"
    >

        <span class="material-icons">
            home
        </span>

        Home

    </a>


    <!-- HOUSEHOLDS -->

    <a
        href="index.php?tab=households"
        class="nav-item <?php
            echo ($current_tab === 'households')
                ? 'active'
                : '';
        ?>"
    >

        <span class="material-icons">
            group
        </span>

        Households

    </a>


    <!-- MESSAGES -->

    <a
        href="index.php?tab=message"
        class="nav-item <?php
            echo ($current_tab === 'message')
                ? 'active'
                : '';
        ?>"
    >

        <span class="material-icons">
            chat
        </span>

        Message

    </a>


    <!-- SETTINGS -->

    <a
        href="index.php?tab=settings"
        class="nav-item <?php
            echo ($current_tab === 'settings')
                ? 'active'
                : '';
        ?>"
    >

        <span class="material-icons">
            settings
        </span>

        Settings

    </a>


    <!-- =====================================================
         LOGGED-IN USER
    ====================================================== -->

    <div class="user-area">

        <span>

            Welcome,

            <strong>
                <?php
                echo htmlspecialchars(
                    $_SESSION["user_name"],
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </strong>

        </span>


        <a
            href="logout.php"
            class="logout"
        >
            Logout
        </a>

    </div>

</nav>


<!-- =========================================================
     MAIN CONTENT
========================================================= -->

<main class="card">


<?php

/* ===========================================================
   HOME TAB
=========================================================== */

if ($current_tab === 'home') {

?>

    <h1>
        Welcome to Your Household
    </h1>

    <p>
        Welcome back,
        <strong>
            <?php
            echo htmlspecialchars(
                $_SESSION["user_name"],
                ENT_QUOTES,
                'UTF-8'
            );
            ?>
        </strong>!
    </p>

    <p>
        Track your shared chores, expenses,
        shopping lists, and calendar events
        right here.
    </p>


<?php

}


/* ===========================================================
   HOUSEHOLDS TAB
=========================================================== */

elseif ($current_tab === 'households') {

?>

    <h1>
        Create a Household
    </h1>

    <div class="form-group">

        <input
            type="text"
            placeholder="e.g., Suite 404, Taylor Hall"
        >

        <button
            class="btn"
            type="button"
        >
            Create Group
        </button>

    </div>


    <h1>
        Invite Roommates
    </h1>

    <div class="form-group">

        <input
            type="email"
            placeholder="roommate@university.edu"
        >

        <button
            class="btn"
            type="button"
        >
            Send Invite
        </button>

    </div>


<?php

}


/* ===========================================================
   MESSAGE TAB
=========================================================== */

elseif ($current_tab === 'message') {

?>

    <h1>
        Household Messages
    </h1>

    <p>
        Send messages to your household below.
    </p>


    <div class="chat-container">


        <!-- =================================================
             MESSAGE DISPLAY
        ================================================== -->

        <div
            class="chat-messages"
            id="chatMessages"
        >


            <!-- STATIC STARTER MESSAGE -->

            <div
                class="chat-bubble"
                style="
                    background:#f3f4f6;
                    color:#1f2937;
                "
            >

                <strong>
                    Alex M.
                </strong>

                •

                <span style="color:#6b7280;">
                    10:14 AM
                </span>

                <br>

                Hey everyone! Who is doing the
                grocery run this week? We are out
                of milk.

            </div>


            <!-- =============================================
                 LOAD SAVED MESSAGES
            ============================================== -->

            <?php

            if (file_exists($msg_file)) {

                $file_contents =
                    file_get_contents($msg_file);


                if (
                    $file_contents !== false
                    &&
                    trim($file_contents) !== ''
                ) {


                    // Break messages apart
                    $saved_messages = preg_split(
                        "/\n---\n/",
                        trim($file_contents)
                    );


                    foreach (
                        $saved_messages
                        as $message
                    ) {


                        $message = trim($message);


                        if ($message !== '') {

                            echo "
                                <div class='chat-bubble'>
                                    {$message}
                                </div>
                            ";

                        }

                    }

                }

            }

            ?>


        </div>


        <!-- =================================================
             SEND MESSAGE FORM
        ================================================== -->

        <form
            method="POST"
            action="index.php?tab=message"
            class="chat-input-bar"
        >

            <input
                type="text"
                name="message_content"
                placeholder="Type a message to your household..."
                required
                autocomplete="off"
            >

            <button
                type="submit"
                name="send_message"
                class="btn"
            >
                Send
            </button>

        </form>


    </div>


<?php

}


/* ===========================================================
   SETTINGS TAB
=========================================================== */

elseif ($current_tab === 'settings') {

?>

    <h1>
        Account Settings
    </h1>

    <p>
        Manage your user account information.
    </p>


    <div class="settings-box">


        <!-- NAME -->

        <div class="settings-row">

            <span class="settings-label">
                Name
            </span>

            <?php
            echo htmlspecialchars(
                $_SESSION["user_name"],
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

        </div>


        <!-- EMAIL -->

        <div class="settings-row">

            <span class="settings-label">
                Email
            </span>

            <?php
            echo htmlspecialchars(
                $_SESSION["user_email"],
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

        </div>


        <!-- USER ID -->

        <div class="settings-row">

            <span class="settings-label">
                User ID
            </span>

            <?php
            echo htmlspecialchars(
                $_SESSION["user_id"],
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

        </div>


        <!-- LOGOUT -->

        <div class="settings-row">

            <a
                href="logout.php"
                class="logout"
            >
                Log out of your account
            </a>

        </div>


    </div>


<?php

}

?>


</main>


<!-- =========================================================
     AUTO-SCROLL CHAT TO BOTTOM
========================================================= -->

<script>

    const chatMessages =
        document.getElementById("chatMessages");

    if (chatMessages) {

        chatMessages.scrollTop =
            chatMessages.scrollHeight;
    }

</script>


</body>

</html>