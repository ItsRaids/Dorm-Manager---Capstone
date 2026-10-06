<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
    // This means you aren't logged in, so it redirects you to the login page.
}

require_once "db.php";

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];
$user_email = $_SESSION["user_email"];


// ============================================================
// TAB NAVIGATION
// ============================================================

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'home';

$allowed_tabs = array(
    "home",
    "households",
    "message",
    "settings"
);

if (!in_array($current_tab, $allowed_tabs)) {
    $current_tab = "home";
}


// ============================================================
// ACTIVE HOUSEHOLD
// ============================================================

$active_household_id = isset($_SESSION["active_household_id"])
    ? $_SESSION["active_household_id"]
    : null;


// ============================================================
// SWITCH HOUSEHOLD
// ============================================================

if (isset($_POST["switch_household"])) {

    $selected_household_id = $_POST["household_id"];

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

        $_SESSION["active_household_id"] = $selected_household_id;
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

    $household_name = $_POST["household_name"];

    if ($household_name == '') {

        $household_error = "Household Name Cannot be blank!";

    } else {

        $allowed_characters = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";

        // Keep generating until we get a unique join code.
        do {

            $join_code = "";

            // Generate 6 characters.
            for ($i = 0; $i < 6; $i++) {

                $random_index = random_int(
                    0,
                    strlen($allowed_characters) - 1
                );

                $random_character =
                    $allowed_characters[$random_index];

                // Add character to the join code.
                $join_code .= $random_character;
            }

            // Check if the code already exists.
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


        // Get the ID of the new household.
        $new_household_id = $pdo->lastInsertId();


        // Add creator as owner.
        $stmt = $pdo->prepare(
            "INSERT INTO household_members (household_id, user_id, role)
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


        // Success message.
        $household_success = "Household created successfully!";
    }
}


// ============================================================
// GET USER'S HOUSEHOLDS
// ============================================================

$stmt = $pdo->prepare(
    "SELECT households.name, households.id
     FROM household_members
     JOIN households
         ON household_members.household_id = households.id
     WHERE household_members.user_id = ?"
);

$stmt->execute([$user_id]);

$my_households = $stmt->fetchAll();

?>

<!-- ============================================================
     NAVIGATION
============================================================ -->

<nav>

    <a href="test.php?tab=home">Home</a>

    <a href="test.php?tab=households">Households</a>

    <a href="test.php?tab=message">Messages</a>

    <a href="test.php?tab=settings">Settings</a>

</nav>


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