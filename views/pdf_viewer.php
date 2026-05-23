    <?php
    session_start();

    // Auth obligatoire
    if (!isset($_SESSION['utilisateur'])) {
        http_response_code(403);
        exit('Accès refusé.');
    }

    require_once __DIR__ . '/../config/dbconnexion.php';
    require_once __DIR__ . '/../models/Memoire.php';

    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); exit('ID invalide.'); }

    $memoire = Memoire::findById($id);

    $idUtilisateur = $_SESSION['utilisateur']['id'] ?? 0;
    $role          = $_SESSION['utilisateur']['role'] ?? '';

    if (!$memoire) {
        http_response_code(404);
        exit('Mémoire introuvable.');
    }

    // Mémoire non validé — accès restreint
    if ($memoire['statut'] !== 'valide') {
        $aAcces = false;

        if ($role === 'professeur') {
            require_once __DIR__ . '/../models/Professeur.php';
            $prof = Professeur::findByUtilisateur($idUtilisateur);
            if ($prof && (
                (int)$prof['id_professeur'] === (int)$memoire['id_maitre_memoire'] ||
                (int)$prof['id_professeur'] === (int)$memoire['id_president_jury']
            )) {
                $aAcces = true;
            }
        }

        if ($role === 'etudiant') {
            require_once __DIR__ . '/../models/Soumettre.php';
            $auteurs = Soumettre::findAuteursByMemoire($memoire['id_memoire']);
            foreach ($auteurs as $a) {
                if ((int)$a['id_utilisateur'] === $idUtilisateur) {
                    $aAcces = true;
                    break;
                }
            }
        }

        if (!$aAcces) {
            http_response_code(403);
            exit('Accès refusé.');
        }
    }

    $fichier = __DIR__ . '/../uploads/memoires/' . basename($memoire['nom_fichier']);
    if (!file_exists($fichier)) {
        http_response_code(404);
        exit('Fichier introuvable.');
    }
    // Lire le PDF et l'encoder en base64
    $pdfData = base64_encode(file_get_contents($fichier));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Lecteur PDF</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #525659; overflow-x: hidden; }
        #toolbar {
            position: sticky; top: 0; z-index: 10;
            background: #323639;
            display: flex; align-items: center; gap: 12px;
            padding: 8px 16px;
            color: #fff; font-family: sans-serif; font-size: 13px;
        }
        #toolbar button {
            background: #525659; color: #fff; border: none;
            padding: 4px 10px; border-radius: 4px; cursor: pointer;
        }
        #toolbar button:hover { background: #6e7478; }
        #page-info { margin: 0 8px; }
        #canvas-container {
            display: flex; flex-direction: column;
            align-items: center; padding: 16px; gap: 12px;
        }
        canvas { box-shadow: 0 2px 8px rgba(0,0,0,0.5); }
    </style>
</head>
<body>
    <div id="toolbar">
        <button onclick="changePage(-1)">◀</button>
        <span id="page-info">Page 1 / ?</span>
        <button onclick="changePage(1)">▶</button>
        <button onclick="changeZoom(-0.2)">−</button>
        <span id="zoom-info">100%</span>
        <button onclick="changeZoom(0.2)">+</button>
    </div>
    <div id="canvas-container"></div>

    <script type="module">
        import * as pdfjsLib from '../assets/build/pdf.mjs';
        pdfjsLib.GlobalWorkerOptions.workerSrc = '../assets/build/pdf.worker.mjs';

        const pdfData = '<?= $pdfData ?>';
        const pdfUrl  = { data: atob(pdfData) };
        let pdfDoc = null, currentPage = 1, scale = 1.2;

        async function loadPDF() {
            pdfDoc = await pdfjsLib.getDocument(pdfUrl).promise;
            document.getElementById('page-info').textContent = `Page ${currentPage} / ${pdfDoc.numPages}`;
            renderPage(currentPage);
        }

        async function renderPage(num) {
            const container = document.getElementById('canvas-container');
            container.innerHTML = '';
            const page  = await pdfDoc.getPage(num);
            const viewport = page.getViewport({ scale });
            const canvas  = document.createElement('canvas');
            canvas.width  = viewport.width;
            canvas.height = viewport.height;
            container.appendChild(canvas);
            await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
            document.getElementById('page-info').textContent = `Page ${num} / ${pdfDoc.numPages}`;
        }

        window.changePage = (delta) => {
            const next = currentPage + delta;
            if (next >= 1 && next <= pdfDoc.numPages) {
                currentPage = next;
                renderPage(currentPage);
            }
        };

        window.changeZoom = (delta) => {
            scale = Math.min(3, Math.max(0.5, scale + delta));
            document.getElementById('zoom-info').textContent = Math.round(scale * 100) + '%';
            renderPage(currentPage);
        };

        // Bloquer clic droit et raccourcis
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (e.ctrlKey && ['s','p','S','P'].includes(e.key)) e.preventDefault();
        });

        loadPDF();
    </script>
</body>
</html>