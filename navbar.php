<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href='//fonts.googleapis.com/css?family=Inter:100,200,300,400,500,600,700,800,900' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="/OctaFlow/assets/css/dark-theme.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="/OctaFlow">OctaFlow</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/OctaFlow/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/OctaFlow/php/sistemas/listar_sistemas.php">Sistemas</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Manutenção
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/OctaFlow/php/manutencoes/form_manutencao.php">+ Checklist Manutenção</a></li>
                        <li><a class="dropdown-item" href="/OctaFlow/php/manutencoes/listar_checklists.php">Checklists Manutenção</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Checklists
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/OctaFlow/php/checklists/checklist.php">Nova Checklist</a></li>
                        <li><a class="dropdown-item" href="/OctaFlow/php/checklists/listar_checklists.php">Checklists</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Empresas
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/OctaFlow/php/empresas/formulario_empresa.php">+ Nova Empresa</a></li>
                        <li><a class="dropdown-item" href="/OctaFlow/php/empresas/listar_empresas.php">Empresas</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>