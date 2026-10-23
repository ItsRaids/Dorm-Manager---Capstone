<?php
session_start();

<<<<<<< HEAD
// ============================================================
// LOGIN PROTECTION
// ============================================================

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
=======
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
    // This means you aren't logged in, so it redirects you to the login page.
>>>>>>> origin/main
}

require_once "db.php";

$user_id = $_SESSION["user_id"];
<<<<<<< HEAD
$user_name = $_SESSION["user_name"] ?? "";
$user_email = $_SESSION["user_email"] ?? "";
=======
$user_name = $_SESSION["user_name"];
$user_email = $_SESSION["user_email"];
>>>>>>> origin/main


// ============================================================
// TAB NAVIGATION
// ============================================================

<<<<<<< HEAD
$current_tab = $_GET["tab"] ?? "home";
=======
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'home';
>>>>>>> origin/main

$allowed_tabs = array(
    "home",
    "households",
    "message",
    "settings"
);

<<<<<<< HEAD
if (!in_array($current_tab, $allowed_tabs, true)) {
=======
if (!in_array($current_tab, $allowed_tabs)) {
>>>>>>> origin/main
    $current_tab = "home";
}


// ============================================================
// ACTIVE HOUSEHOLD
// ============================================================

<<<<<<< HEAD
$active_household_id = $_SESSION["active_household_id"] ?? null;

// Verify that the user still belongs to the active household.
if ($active_household_id !== null) {

    $stmt = $pdo->prepare(
        "SELECT id
         FROM household_members
         WHERE user_id = ?
         AND household_id = ?"
    );

    $stmt->execute([
        $user_id,
        $active_household_id
    ]);

    if (!$stmt->fetch()) {
        unset($_SESSION["active_household_id"]);
        $active_household_id = null;
    }
}
=======
$active_household_id = isset($_SESSION["active_household_id"])
    ? $_SESSION["active_household_id"]
    : null;
>>>>>>> origin/main


// ============================================================
// SWITCH HOUSEHOLD
// ============================================================

if (isset($_POST["switch_household"])) {

<<<<<<< HEAD
    $selected_household_id = $_POST["household_id"] ?? null;
=======
    $selected_household_id = $_POST["household_id"];
>>>>>>> origin/main

    $stmt = $pdo->prepare(
        "SELECT id
         FROM household_members
         WHERE user_id = ?
         AND household_id = ?"
    );

    $stmt->execute([
        $user_id,
        $selected_household_id
    ]);

    $membership = $stmt->fetch();

    if ($membership) {

<<<<<<< HEAD
        $_SESSION["active_household_id"] = $membership["id"] === null
            ? null
            : $selected_household_id;

=======
        $_SESSION["active_household_id"] = $selected_household_id;
>>>>>>> origin/main
        $active_household_id = $selected_household_id;

    } else {

        $household_error =
            "Sorry, you aren't a member of the selected household.";
    }
}


// ============================================================
// CREATE HOUSEHOLD
// ============================================================

if (isset($_POST["create_household"])) {

<<<<<<< HEAD
    $household_name = trim($_POST["household_name"] ?? "");

    if ($household_name === "") {

        $household_error = "Household name cannot be blank!";
=======
    $household_name = $_POST["household_name"];

    if ($household_name == '') {

        $household_error = "Household Name Cannot be blank!";
>>>>>>> origin/main

    } else {

        $allowed_characters = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";

<<<<<<< HEAD
        // Generate codes until an unused one is found.
=======
        // Keep generating until we get a unique join code.
>>>>>>> origin/main
        do {

            $join_code = "";

<<<<<<< HEAD
=======
            // Generate 6 characters.
>>>>>>> origin/main
            for ($i = 0; $i < 6; $i++) {

                $random_index = random_int(
                    0,
                    strlen($allowed_characters) - 1
                );

<<<<<<< HEAD
                $join_code .= $allowed_characters[$random_index];
            }

=======
                $random_character =
                    $allowed_characters[$random_index];

                // Add character to the join code.
                $join_code .= $random_character;
            }

            // Check if the code already exists.
>>>>>>> origin/main
            $stmt = $pdo->prepare(
                "SELECT id
                 FROM households
                 WHERE join_code = ?"
            );

            $stmt->execute([$join_code]);

            $existing_code = $stmt->fetch();

        } while ($existing_code);


        // Create the household.
        $stmt = $pdo->prepare(
            "INSERT INTO households (name, join_code, created_by)
             VALUES (?, ?, ?)"
        );

        $stmt->execute([
            $household_name,
            $join_code,
            $user_id
        ]);

<<<<<<< HEAD
        $new_household_id = $pdo->lastInsertId();


        // Add the creator as owner.
        $stmt = $pdo->prepare(
            "INSERT INTO household_members
                (household_id, user_id, role)
=======

        // Get the ID of the new household.
        $new_household_id = $pdo->lastInsertId();


        // Add creator as owner.
        $stmt = $pdo->prepare(
            "INSERT INTO household_members (household_id, user_id, role)
>>>>>>> origin/main
             VALUES (?, ?, ?)"
        );

        $stmt->execute([
            $new_household_id,
            $user_id,
            "owner"
        ]);


        // Make the new household active.
        $_SESSION["active_household_id"] = $new_household_id;
        $active_household_id = $new_household_id;

<<<<<<< HEAD
        $household_success = "Household created successfully!";
        $created_join_code = $join_code;
=======

        // Success message.
        $household_success = "Household created successfully!";
>>>>>>> origin/main
    }
}


// ============================================================
<<<<<<< HEAD
// JOIN HOUSEHOLD
// ============================================================

if (isset($_POST["join_household"])) {

    $join_code = strtoupper(trim($_POST["join_code"] ?? ""));

    if ($join_code === "") {

        $household_error = "Join code cannot be blank!";

    } else {

        $stmt = $pdo->prepare(
            "SELECT id
             FROM households
             WHERE join_code = ?"
        );

        $stmt->execute([$join_code]);

        $household = $stmt->fetch();

        if ($household) {

            $household_id = $household["id"];

            // Check whether the user is already a member.
            $stmt = $pdo->prepare(
                "SELECT id
                 FROM household_members
                 WHERE user_id = ?
                 AND household_id = ?"
            );

            $stmt->execute([
                $user_id,
                $household_id
            ]);

            $membership = $stmt->fetch();

            if ($membership) {

                $household_error =
                    "You are already a member of this household.";

            } else {

                $stmt = $pdo->prepare(
                    "INSERT INTO household_members
                        (household_id, user_id, role)
                     VALUES (?, ?, ?)"
                );

                $stmt->execute([
                    $household_id,
                    $user_id,
                    "member"
                ]);

                $_SESSION["active_household_id"] = $household_id;
                $active_household_id = $household_id;

                $household_success =
                    "Successfully joined the household!";
            }

        } else {

            $household_error =
                "Invalid join code. Please try again.";
        }
    }
}


// ============================================================
// UPDATE PROFILE INFORMATION
// ============================================================

if (isset($_POST["update_profile"])) {

    $updated_name = trim($_POST["name"] ?? "");
    $updated_email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $birthday = trim($_POST["birthday"] ?? "");
    $bio = trim($_POST["bio"] ?? "");

    if ($updated_name === "" || $updated_email === "") {

        $profile_error = "Name and email cannot be blank.";

    } elseif (!filter_var($updated_email, FILTER_VALIDATE_EMAIL)) {

        $profile_error = "Please enter a valid email address.";

    } elseif (strlen($phone) > 30) {

        $profile_error = "Phone number cannot exceed 30 characters.";

    } elseif (strlen($bio) > 500) {

        $profile_error = "Bio cannot exceed 500 characters.";

    } elseif (
        $birthday !== "" &&
        (
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthday) ||
            !checkdate(
                (int) substr($birthday, 5, 2),
                (int) substr($birthday, 8, 2),
                (int) substr($birthday, 0, 4)
            ) ||
            $birthday > date("Y-m-d")
        )
    ) {

        $profile_error =
            "Please enter a valid birthday that is not in the future.";

    } else {

        // Make sure another account is not using this email.
        $stmt = $pdo->prepare(
            "SELECT id
             FROM users
             WHERE email = ?
             AND id <> ?"
        );

        $stmt->execute([
            $updated_email,
            $user_id
        ]);

        $email_exists = $stmt->fetch();

        if ($email_exists) {

            $profile_error =
                "That email address is already used by another account.";

        } else {

            // Update only the logged-in user's account.
            $stmt = $pdo->prepare(
                "UPDATE users
                 SET name = ?,
                     email = ?,
                     phone = ?,
                     birthday = ?,
                     bio = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $updated_name,
                $updated_email,
                $phone === "" ? null : $phone,
                $birthday === "" ? null : $birthday,
                $bio === "" ? null : $bio,
                $user_id
            ]);

            // Update session details.
            $_SESSION["user_name"] = $updated_name;
            $_SESSION["user_email"] = $updated_email;

            $user_name = $updated_name;
            $user_email = $updated_email;

            $profile_success = "Profile updated successfully!";
        }
    }
}


// ============================================================
// GET THE CURRENT USER'S PROFILE
// ============================================================

$stmt = $pdo->prepare(
    "SELECT name, email, phone, birthday, bio
     FROM users
     WHERE id = ?"
);

$stmt->execute([$user_id]);

$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {

    $profile = [
        "name" => $user_name,
        "email" => $user_email,
        "phone" => "",
        "birthday" => "",
        "bio" => ""
    ];
}


// ============================================================
// GET THE USER'S HOUSEHOLDS
// ============================================================

$stmt = $pdo->prepare(
    "SELECT households.name, households.id, households.join_code
=======
// GET USER'S HOUSEHOLDS
// ============================================================

$stmt = $pdo->prepare(
    "SELECT households.name, households.id
>>>>>>> origin/main
     FROM household_members
     JOIN households
         ON household_members.household_id = households.id
     WHERE household_members.user_id = ?"
);

$stmt->execute([$user_id]);

$my_households = $stmt->fetchAll();

<<<<<<< HEAD

// ============================================================
// GET MEMBERS OF THE ACTIVE HOUSEHOLD
// ============================================================

$household_members = [];

if ($active_household_id !== null) {

    $stmt = $pdo->prepare(
        "SELECT users.name, household_members.role
         FROM household_members
         JOIN users
             ON household_members.user_id = users.id
         WHERE household_members.household_id = ?"
    );

    $stmt->execute([$active_household_id]);

    $household_members = $stmt->fetchAll();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DormHub</title>

    <style>
        :root {
            --dormhub-purple: #7C3AED;
            --dormhub-purple-hover: #6D28D9;
            --dormhub-purple-light: #EDE9FE;
            --dormhub-gray: #F3F1F7;
            --dormhub-border: #DDD6E8;
            --dormhub-text: #302A3A;
            --dormhub-muted: #716B7C;
            --dormhub-white: #FFFFFF;
            --dormhub-success: #15803D;
            --dormhub-error: #B91C1C;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--dormhub-gray);
            color: var(--dormhub-text);
        }

        /* HEADER AND LOGO */

        .site-header {
            display: flex;
            align-items: center;
            background: var(--dormhub-white);
            border-bottom: 1px solid var(--dormhub-border);
            padding: 16px 24px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-icon {
            display: block;
            width: 48px;
            height: 48px;
            flex-shrink: 0;
        }

        .brand-name {
            color: var(--dormhub-text);
            font-size: 27px;
            font-weight: 800;
            letter-spacing: -0.8px;
        }

        .brand-accent {
            color: var(--dormhub-purple);
        }

        .brand:hover .brand-accent {
            color: var(--dormhub-purple-hover);
        }

        /* NAVIGATION */

        nav {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 14px 24px;
            background: var(--dormhub-white);
            border-bottom: 1px solid var(--dormhub-border);
        }

        nav a {
            padding: 10px 16px;
            text-decoration: none;
            color: var(--dormhub-muted);
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        nav a:hover {
            background: var(--dormhub-purple-light);
            color: var(--dormhub-purple);
        }

        nav a.active {
            background: var(--dormhub-purple);
            color: white;
        }

        /* PAGE LAYOUT */

        main {
            max-width: 950px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-panel {
            background: var(--dormhub-white);
            border: 1px solid var(--dormhub-border);
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 4px 14px rgba(48, 42, 58, 0.04);
        }

        h1 {
            margin-top: 0;
            color: var(--dormhub-text);
            font-size: 28px;
        }

        h2 {
            margin-top: 28px;
            font-size: 20px;
        }

        h3 {
            margin-top: 0;
            margin-bottom: 12px;
        }

        /* FORM FIELDS */

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        textarea {
            width: 100%;
            max-width: 400px;
            padding: 11px 13px;
            margin: 5px 0 12px;
            border: 1px solid var(--dormhub-border);
            border-radius: 7px;
            font-size: 15px;
            font-family: inherit;
            color: var(--dormhub-text);
            background: white;
        }

        textarea {
            resize: vertical;
            max-width: 600px;
        }

        input:focus,
        textarea:focus {
            outline: 2px solid #C4B5FD;
            border-color: var(--dormhub-purple);
        }

        label {
            font-size: 14px;
            font-weight: 600;
        }

        /* BUTTONS */

        .primary-button,
        .copy-invite-button {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background: var(--dormhub-purple);
            color: white;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .primary-button:hover,
        .copy-invite-button:hover {
            background: var(--dormhub-purple-hover);
            transform: translateY(-1px);
        }

        button:focus-visible,
        nav a:focus-visible {
            outline: 3px solid #C4B5FD;
            outline-offset: 3px;
        }

        .copy-status {
            color: var(--dormhub-success);
            margin-left: 8px;
            font-size: 13px;
        }

        /* HOUSEHOLD CARDS */

        .household-card {
            margin: 14px 0;
            padding: 18px;
            border: 1px solid var(--dormhub-border);
            border-radius: 10px;
            background: #FCFBFE;
        }

        .household-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-top: 12px;
        }

        .active-label {
            display: inline-block;
            color: var(--dormhub-purple);
            background: var(--dormhub-purple-light);
            border-radius: 20px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        /* HOUSEHOLD MEMBERS */

        .member-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin: 10px 0;
            padding: 12px 14px;
            background: var(--dormhub-gray);
            border-radius: 8px;
        }

        .member-role {
            color: var(--dormhub-purple);
            background: var(--dormhub-purple-light);
            border-radius: 20px;
            padding: 5px 9px;
            font-size: 12px;
            font-weight: 600;
        }

        /* MESSAGES */

        .success-message {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: var(--dormhub-success);
            padding: 12px;
            border-radius: 8px;
        }

        .error-message {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: var(--dormhub-error);
            padding: 12px;
            border-radius: 8px;
        }

        .muted-text {
            color: var(--dormhub-muted);
        }

        /* MOBILE LAYOUT */

        @media (max-width: 600px) {
            .site-header {
                padding: 16px;
            }

            nav {
                padding: 12px 16px;
                gap: 4px;
            }

            nav a {
                padding: 9px 10px;
                font-size: 14px;
            }

            main {
                margin: 18px auto;
                padding: 0 12px;
            }

            .page-panel {
                padding: 18px;
            }

            .brand-name {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<!-- ============================================================
     DORMHUB LOGO
============================================================ -->

<header class="site-header">

    <a
        class="brand"
        href="test.php?tab=home"
        aria-label="DormHub home"
    >

        <svg
            class="brand-icon"
            viewBox="0 0 48 48"
            role="img"
            aria-label="DormHub house logo"
        >
            <rect
                width="48"
                height="48"
                rx="13"
                fill="#7C3AED"
            />

            <path
                d="M9 22 L24 9 L39 22"
                fill="none"
                stroke="#FFFFFF"
                stroke-width="3.2"
                stroke-linecap="round"
                stroke-linejoin="round"
            />

            <path
                d="M14 22 V37 H34 V22"
                fill="none"
                stroke="#FFFFFF"
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
            />

            <path
                d="M17 30 L24 26 L31 30"
                fill="none"
                stroke="#DDD6E8"
                stroke-width="2.2"
                stroke-linecap="round"
                stroke-linejoin="round"
            />

            <circle cx="17" cy="30" r="3" fill="#FFFFFF"/>
            <circle cx="24" cy="26" r="3" fill="#DDD6E8"/>
            <circle cx="31" cy="30" r="3" fill="#FFFFFF"/>
        </svg>

        <span class="brand-name">
            Dorm<span class="brand-accent">Hub</span>
        </span>

    </a>

</header>


=======
?>

>>>>>>> origin/main
<!-- ============================================================
     NAVIGATION
============================================================ -->

<nav>

<<<<<<< HEAD
    <a
        href="test.php?tab=home"
        class="<?php echo $current_tab === 'home' ? 'active' : ''; ?>"
    >Home</a>

    <a
        href="test.php?tab=households"
        class="<?php echo $current_tab === 'households' ? 'active' : ''; ?>"
    >Households</a>

    <a
        href="test.php?tab=message"
        class="<?php echo $current_tab === 'message' ? 'active' : ''; ?>"
    >Messages</a>

    <a
        href="test.php?tab=settings"
        class="<?php echo $current_tab === 'settings' ? 'active' : ''; ?>"
    >Settings</a>

    <a href="logout.php">Logout</a>
=======
    <a href="test.php?tab=home">Home</a>

    <a href="test.php?tab=households">Households</a>

    <a href="test.php?tab=message">Messages</a>

    <a href="test.php?tab=settings">Settings</a>
>>>>>>> origin/main

</nav>


<<<<<<< HEAD
<main>

<?php if ($current_tab === "home") { ?>

    <section class="page-panel">

        <h1>Welcome to Your Homepage</h1>

        <p>
            Welcome,
            <?php echo htmlspecialchars($user_name); ?>!
        </p>

        <p class="muted-text">
            Manage your household, organize tasks, and stay connected
            with your roommates.
        </p>

    </section>


<?php } elseif ($current_tab === "households") { ?>

    <section class="page-panel">

        <h1>Your Households</h1>

        <p class="muted-text">
            Create a household or join one using an invitation code.
        </p>


        <!-- CREATE HOUSEHOLD FORM -->

        <h2>Create a Household</h2>

        <form method="POST" action="test.php?tab=households">

            <input
                type="text"
                name="household_name"
                placeholder="Enter household name"
                required
            >

            <br>

            <button
                type="submit"
                name="create_household"
                value="1"
                class="primary-button"
            >
                Create Household
            </button>

        </form>


        <!-- JOIN HOUSEHOLD FORM -->

        <h2>Join a Household</h2>

        <form method="POST" action="test.php?tab=households">

            <input
                type="text"
                name="join_code"
                placeholder="Enter six-character join code"
                maxlength="6"
                required
                value="<?php
                    echo htmlspecialchars(
                        $_POST["join_code"] ?? $_GET["join_code"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    );
                ?>"
            >

            <br>

            <button
                type="submit"
                name="join_household"
                value="1"
                class="primary-button"
            >
                Join Household
            </button>

        </form>


        <!-- SUCCESS MESSAGE -->

        <?php if (isset($household_success)) { ?>

            <p class="success-message">
                <?php echo htmlspecialchars($household_success); ?>
            </p>

            <?php if (isset($created_join_code)) { ?>

                <p>
                    Your household join code:
                    <strong>
                        <?php echo htmlspecialchars($created_join_code); ?>
                    </strong>
                </p>

            <?php } ?>

        <?php } ?>


        <!-- ERROR MESSAGE -->

        <?php if (isset($household_error)) { ?>

            <p class="error-message">
                <?php echo htmlspecialchars($household_error); ?>
            </p>

        <?php } ?>


        <!-- EXISTING HOUSEHOLDS -->

        <h2>My Households</h2>

        <?php if (empty($my_households)) { ?>

            <p class="muted-text">
                You haven't joined any households yet.
            </p>

        <?php } else { ?>

            <?php foreach ($my_households as $household) {

                $household_name = $household["name"];
                $household_id = $household["id"];

                $invite_link =
                    "http://localhost/dormhub/test.php?tab=households&join_code="
                    . urlencode($household["join_code"]);

            ?>

                <div class="household-card">

                    <h3>
                        <?php echo htmlspecialchars($household_name); ?>
                    </h3>

                    <div class="household-actions">

                        <?php if ($household_id == $active_household_id) { ?>

                            <span class="active-label">ACTIVE</span>

                        <?php } else { ?>

                            <form
                                method="POST"
                                action="test.php?tab=households"
                                style="display:inline;"
                            >

                                <input
                                    type="hidden"
                                    name="household_id"
                                    value="<?php
                                        echo htmlspecialchars(
                                            $household_id,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );
                                    ?>"
                                >

                                <button
                                    type="submit"
                                    name="switch_household"
                                    value="1"
                                    class="primary-button"
                                >
                                    Switch
                                </button>

                            </form>

                        <?php } ?>


                        <!-- COPY INVITE LINK BUTTON -->

                        <button
                            type="button"
                            class="copy-invite-button"
                            data-invite-link="<?php
                                echo htmlspecialchars(
                                    $invite_link,
                                    ENT_QUOTES,
                                    "UTF-8"
                                );
                            ?>"
                            onclick="copyInviteLink(this)"
                        >
                            Copy Invite Link
                        </button>

                        <span class="copy-status" aria-live="polite"></span>

                    </div>

                </div>

            <?php } ?>

        <?php } ?>


        <!-- HOUSEHOLD MEMBERS -->

        <h2>Household Members</h2>

        <?php if ($active_household_id === null) { ?>

            <p class="muted-text">
                No active household selected.
                Create or switch to a household first.
            </p>

        <?php } elseif (empty($household_members)) { ?>

            <p class="muted-text">
                No household members found.
            </p>

        <?php } else { ?>

            <?php foreach ($household_members as $member) { ?>

                <div class="member-row">

                    <strong>
                        <?php echo htmlspecialchars($member["name"]); ?>
                    </strong>

                    <span class="member-role">
                        <?php echo htmlspecialchars($member["role"]); ?>
                    </span>

                </div>

            <?php } ?>

        <?php } ?>

    </section>


<?php } elseif ($current_tab === "message") { ?>

    <section class="page-panel">

        <h1>Household Messages</h1>

        <p class="muted-text">
            Messaging features will be added here.
        </p>

    </section>


<?php } elseif ($current_tab === "settings") { ?>

    <section class="page-panel">

        <h1>Account Settings</h1>

        <h2>Profile Information</h2>

        <p class="muted-text">
            Name and email are required. Phone, birthday, and bio are optional.
        </p>


        <!-- PROFILE SUCCESS MESSAGE -->

        <?php if (isset($profile_success)) { ?>

            <p class="success-message">
                <?php echo htmlspecialchars($profile_success); ?>
            </p>

        <?php } ?>


        <!-- PROFILE ERROR MESSAGE -->

        <?php if (isset($profile_error)) { ?>

            <p class="error-message">
                <?php echo htmlspecialchars($profile_error); ?>
            </p>

        <?php } ?>


        <!-- PROFILE FORM -->

        <form method="POST" action="test.php?tab=settings">

            <label for="name">Display Name</label><br>

            <input
                type="text"
                id="name"
                name="name"
                maxlength="100"
                value="<?php
                    echo htmlspecialchars(
                        $profile["name"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    );
                ?>"
                required
            >

            <br>

            <label for="email">Email Address</label><br>

            <input
                type="email"
                id="email"
                name="email"
                value="<?php
                    echo htmlspecialchars(
                        $profile["email"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    );
                ?>"
                required
            >

            <br>

            <label for="phone">Phone Number (Optional)</label><br>

            <input
                type="tel"
                id="phone"
                name="phone"
                maxlength="30"
                placeholder="Enter your phone number"
                value="<?php
                    echo htmlspecialchars(
                        $profile["phone"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    );
                ?>"
            >

            <br>

            <label for="birthday">Birthday (Optional)</label><br>

            <input
                type="date"
                id="birthday"
                name="birthday"
                value="<?php
                    echo htmlspecialchars(
                        $profile["birthday"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    );
                ?>"
            >

            <br>

            <label for="bio">About Me (Optional)</label><br>

            <textarea
                id="bio"
                name="bio"
                maxlength="500"
                rows="4"
                placeholder="Tell your roommates a little about yourself"
            ><?php
                echo htmlspecialchars(
                    $profile["bio"] ?? "",
                    ENT_QUOTES,
                    "UTF-8"
                );
            ?></textarea>

            <br><br>

            <button
                type="submit"
                name="update_profile"
                value="1"
                class="primary-button"
            >
                Save Changes
            </button>

        </form>

    </section>

<?php } ?>

</main>


<!-- ============================================================
     COPY INVITE LINK FUNCTION
============================================================ -->

<script>
async function copyInviteLink(button) {

    const inviteLink = button.dataset.inviteLink;
    const status = button.nextElementSibling;

    try {

        await navigator.clipboard.writeText(inviteLink);

        button.textContent = "Copied!";

        if (status) {
            status.textContent = "Link copied!";
        }

        setTimeout(() => {

            button.textContent = "Copy Invite Link";

            if (status) {
                status.textContent = "";
            }

        }, 2000);

    } catch (error) {

        window.prompt("Copy this invite link:", inviteLink);
    }
}
</script>

</body>
</html>
=======
<!-- ============================================================
     TAB CONTENT
============================================================ -->

<?php if ($current_tab === 'home') { ?>

    <h1>Welcome to Your Homepage</h1>


<?php } elseif ($current_tab === 'households') { ?>

    <h1>Welcome to Your Households</h1>


    <!-- CREATE HOUSEHOLD FORM -->

    <form method="POST">

        <input
            type="text"
            name="household_name"
            placeholder="Household Name"
        >

        <button
            type="submit"
            name="create_household"
            value="1"
        >
            Create Household
        </button>

    </form>


    <!-- SUCCESS MESSAGE -->

    <?php if (isset($household_success)) { ?>

        <p>
            <?php echo htmlspecialchars($household_success); ?>
        </p>

        <p>
            Join Code:
            <?php echo htmlspecialchars($join_code); ?>
        </p>

    <?php } ?>


    <!-- ERROR MESSAGE -->

    <?php if (isset($household_error)) { ?>

        <p>
            <?php echo htmlspecialchars($household_error); ?>
        </p>

    <?php } ?>


    <!-- EXISTING HOUSEHOLDS -->

    <?php foreach ($my_households as $household) {

        $household_name = $household["name"];
        $household_id = $household["id"];


        if ($household_id == $active_household_id) {

            $active = true;

        } else {

            $active = false;

        }


        echo "<h3>" . htmlspecialchars($household_name) . "</h3>";


        if ($active) {

            echo "ACTIVE";

        } else {

            ?>

            <form method="POST">

                <input
                    type="hidden"
                    name="household_id"
                    value="<?php echo htmlspecialchars($household_id); ?>"
                >

                <button
                    type="submit"
                    name="switch_household"
                    value="1"
                >
                    Switch
                </button>

            </form>

            <?php
        }
    }
    ?>


<?php } elseif ($current_tab === 'message') { ?>

    <h1>Household Messages</h1>


<?php } elseif ($current_tab === 'settings') { ?>

    <h1>Account Settings</h1>

<?php } ?>
>>>>>>> origin/main
