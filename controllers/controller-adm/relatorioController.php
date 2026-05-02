<?php
// controllers/controller-adm/relatorioController.php

require_once __DIR__ . '/../../model/RelatorioModel.php';

class RelatorioController {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function index() {
        $erros = [];
        $relatorioModel = new RelatorioModel($this->pdo);

        // Cada consulta é independente - falha parcial não derruba tudo
        try { $resumo = $relatorioModel->resumoGeral(); }
        catch (Exception $e) { $resumo = array_fill_keys(['total_projetos','projetos_ativos','projetos_concluidos','total_usuarios','usuarios_ativos','total_participacoes','total_producoes','producoes_aprovadas','total_acessos'], 0); $erros[] = 'resumo: '.$e->getMessage(); }

        try { $projetosPorStatus = $relatorioModel->projetosPorStatus(); }
        catch (Exception $e) { $projetosPorStatus = []; $erros[] = $e->getMessage(); }

        try { $projetosPorTipo = $relatorioModel->projetosPorTipo(); }
        catch (Exception $e) { $projetosPorTipo = []; $erros[] = $e->getMessage(); }

        try { $usuariosPorPerfil = $relatorioModel->usuariosPorPerfil(); }
        catch (Exception $e) { $usuariosPorPerfil = []; $erros[] = $e->getMessage(); }

        try { $topProjetos = $relatorioModel->topProjetosPorParticipantes(10); }
        catch (Exception $e) { $topProjetos = []; $erros[] = $e->getMessage(); }

        try { $producoesPorTipo = $relatorioModel->producoesPorTipo(); }
        catch (Exception $e) { $producoesPorTipo = []; $erros[] = $e->getMessage(); }

        try { $acessosPorMes = $relatorioModel->acessosPorMes(); }
        catch (Exception $e) { $acessosPorMes = []; $erros[] = $e->getMessage(); }

        try { $participacoesPorFuncao = $relatorioModel->participacoesPorFuncao(); }
        catch (Exception $e) { $participacoesPorFuncao = []; $erros[] = $e->getMessage(); }

        try { $projetosPorMes = $relatorioModel->projetosPorMes(); }
        catch (Exception $e) { $projetosPorMes = []; $erros[] = $e->getMessage(); }

        require __DIR__ . '/../../views/view-adm/relatorios.view.php';
    }
}
?>
