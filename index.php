<!DOCTYPE html>
<html lang="ru">
<?php include 'partials/head.php'; ?>
<body class="bg-light">
<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <div class="card p-4 shadow" style="max-width: 500px; margin: 0 auto;">
        <h2 class="text-center">Голосование</h2>

        <?php
        session_start();
        $languages = ["C++", "C#", "Java", "PHP", "JavaScript"];
        $votesFile = 'votes.txt';

        if (!file_exists($votesFile)) {
            $initialVotes = array_fill(0, count($languages), 0);
            file_put_contents($votesFile, json_encode($initialVotes));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selectedLanguage = $_POST['language'];
            $lastVoteTime = isset($_COOKIE['last_vote']) ? $_COOKIE['last_vote'] : 0;
            $currentTime = time();

            $timeRemaining = 60 - ($currentTime - $lastVoteTime);

            if ($timeRemaining > 0) {
                echo "<div class='alert alert-danger mt-3'>Вы уже голосовали! Следуещее голосование возможно через $timeRemaining секунд.</div>";
            } else {
                $votes = json_decode(file_get_contents($votesFile), true);
                $votes[array_search($selectedLanguage, $languages)]++;
                file_put_contents($votesFile, json_encode($votes));
                setcookie('last_vote', $currentTime, $currentTime + 60);
                echo "<div class='alert alert-success mt-3'>Спасибо за ваш голос!</div>";
            }
        }

        $votes = json_decode(file_get_contents($votesFile), true);
        $totalVotes = array_sum($votes) ?: 1;
        ?>

        <form action="" method="post" class="mt-3">
            <label for="language-select" class="form-label">Выберите язык:</label>
            <select name="language" id="language-select" class="form-select" required>
                <?php foreach ($languages as $language): ?>
                    <option value="<?= $language ?>"><?= $language ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary w-100 mt-3">Голосовать</button>
        </form>
    </div>

    <div class="mt-5">
        <h3 class="text-center">Результаты голосования</h3>
        <div class="row mt-3">
            <?php foreach ($votes as $index => $voteCount):
                $percentage = round(($voteCount / $totalVotes) * 100);
                ?>
                <div class="col-12 mb-2">
                    <div class="d-flex justify-content-between">
                        <span><?= $languages[$index] ?></span>
                        <span><?= $percentage ?>%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-info" role="progressbar"
                             style="width: <?= $percentage ?>%;"
                             aria-valuenow="<?= $percentage ?>"
                             aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</body>
</html>
