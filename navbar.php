<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
<nav class="navbar navbar-expand-lg" style="background: linear-gradient(90deg, #00bcd4, #8e44ad);">
      <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">OctaFlow</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link text-white" aria-current="page" href="/OctaFlow/index.php">Home</a>
            </li>
            <li class="nav-item">
            <a class="nav-link text-white" href="/OctaFlow/php/sistemas/listar_sistemas.php">Sistemas</a>
            </li>
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Manutenção
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/OctaFlow/php/manutencoes/form_manutencao.php">+ Checklist Manutenção</a></li>
                <li><a class="dropdown-item" href="/OctaFlow/php/manutencoes/listar_checklists.php">Checklists Manutenção</a></li>
            </ul>
            </li>
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Checklists
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/OctaFlow/php/checklists/checklist.php">Nova Checklist</a></li>
                <li><a class="dropdown-item" href="/OctaFlow/php/checklists/listar_checklists.php">Checklists</a></li>
            </ul>
            </li>
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Empresas
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/OctaFlow/php/empresas/listar_empresas.php">Empresas Cadastradas</a></li>
                <li><a class="dropdown-item" href="/OctaFlow/php/empresas/formulario_empresa.php">Cadastrar Empresa</a></li>
            </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>