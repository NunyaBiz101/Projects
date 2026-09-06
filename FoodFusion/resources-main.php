<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);

require_once 'app/config/database.php';

if (!$conn) {
    die("Database connection not established.");
}

try {
    $stmt = $conn->prepare("SELECT * FROM recipes ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $recipes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    die("Database query failed: " . $e->getMessage());
}
?>

<section class="resources-section">
    <div class="container">
        <h1 class="section-heading text-center mb-5">Resources</h1>

        <div class="row mb-5" id="culinary">
            <div class="col-12">
                <h2 class="h3 mb-4 text-center">Culinary Resources</h2>
                <p class="text-center mb-4">Get all the culinary resources you need here for a better time in the kitchen.</p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/P6W8kwmwcno" title="Cooking Tutorial 1" allowfullscreen></iframe>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Learn to Cook</h5>
                        <p class="card-text">A beginner-friendly introduction to the cooking universe.</p>
                        <a href="https://www.youtube.com/watch?v=P6W8kwmwcno" class="btn btn-custom" target="_blank">Watch on YouTube</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/H_erG7HSK0A" title="Cooking Tutorial 2" allowfullscreen></iframe>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Food Hacks</h5>
                        <p class="card-text">Learn easy tricks to better your cooking skills and food experience.</p>
                        <a href="https://www.youtube.com/watch?v=H_erG7HSK0A" class="btn btn-custom" target="_blank">Watch on YouTube</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/bx0BlXeNet0?start=343" title="Cooking Tutorial 3" allowfullscreen></iframe>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Kitchen Hacks</h5>
                        <p class="card-text">Learn some hacks to make your cooking easier and faster.</p>
                        <a href="https://www.youtube.com/watch?v=bx0BlXeNet0&t=343s" class="btn btn-custom" target="_blank">Watch on YouTube</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <h3 class="text-center mb-4">Downloadable Recipe Cards</h3>
                <p class="text-center mb-4">Print and use these recipe cards for your favorite dishes.</p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <?php if (empty($recipes)): ?>
                <div class="col-12">
                    <p class="text-center">No recipes available yet. Check back soon!</p>
                </div>
            <?php else: ?>
                <?php foreach ($recipes as $recipe): ?>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-file-earmark-text mb-3" viewBox="0 0 16 16" style="color: #865029;">
                                    <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                                    <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                </svg>
                                <h5 class="card-title"><?= htmlspecialchars($recipe['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars(substr($recipe['instructions'], 0, 100)) ?>...</p>
                                <a href="download_recipe.php?id=<?= $recipe['id'] ?>" class="btn btn-custom">Download Card</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="row mb-5" id="educational">
            <div class="col-12">
                <h2 class="h3 mb-4 text-center">Educational Resources</h2>
                <p class="text-center mb-4">Learn about renewable energy and sustainable practices through videos and downloadable guides.</p>
            </div>
        </div>

        <h3 class="text-center mb-4">Educational Videos</h3>
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/1kUE0BZtTRc" title="Renewable Energy 101" allowfullscreen></iframe>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Renewable Energy 101</h5>
                        <p class="card-text">Learn how renewable energy works and the importance of renewable energy from National Geographic.</p>
                        <a href="https://www.youtube.com/watch?v=1kUE0BZtTRc" class="btn btn-custom" target="_blank">Watch on YouTube</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/VfowJHJz6-s" title="What is Renewable Energy" allowfullscreen></iframe>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">What is Renewable Energy?</h5>
                        <p class="card-text">Learn what is renewable energy.</p>
                        <a href="https://www.youtube.com/watch?v=VfowJHJz6-s" class="btn btn-custom" target="_blank">Watch on YouTube</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/44Wp3WE1AHs" title="7 Types of Renewable Energy" allowfullscreen></iframe>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">7 Types of Renewable Energy</h5>
                        <p class="card-text">Learn about the different types of renewable energy and the benefits of each.</p>
                        <a href="https://www.youtube.com/watch?v=44Wp3WE1AHs" class="btn btn-custom" target="_blank">Watch on YouTube</a>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="text-center mb-4">Downloadable Guides</h3>
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-file-earmark-pdf mb-3" viewBox="0 0 16 16" style="color: #27ae60;">
                            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                            <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
                        </svg>
                        <h5 class="card-title">Renewable Energy Overview</h5>
                        <p class="card-text">A quick overview about renewable energy from NREL.</p>
                        <a href="pdfs/renewable-energy-overview.pdf" class="btn btn-custom" download>Download PDF</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-file-earmark-pdf mb-3" viewBox="0 0 16 16" style="color: #27ae60;">
                            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                            <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
                        </svg>
                        <h5 class="card-title">Renewable Energy Basics</h5>
                        <p class="card-text">Learn about renewable energy technologies and their applications.</p>
                        <a href="pdfs/renewable-energy-basics.pdf" class="btn btn-custom" download>Download PDF</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-file-earmark-pdf mb-3" viewBox="0 0 16 16" style="color: #27ae60;">
                            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                            <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
                        </svg>
                        <h5 class="card-title">Clean Energy Guide</h5>
                        <p class="card-text">A comprehensive overview of clean energy technologies.</p>
                        <a href="pdfs/clean-energy-guide.pdf" class="btn btn-custom" download>Download PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>