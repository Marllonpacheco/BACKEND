<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header border-danger">
        <h5 class="modal-title text-danger">🗑️ Confirmar Exclusão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>Tem certeza que deseja excluir o aluno
          <strong id="modalNome"></strong>?
        </p>
        <p class="text-muted small">Esta ação não poderá ser desfeita.</p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary"
                data-bs-dismiss="modal">Cancelar</button>

        <!-- Link de exclusão — o href é preenchido pelo JS -->
        <a id="btnConfirmarExcluir" href="#"
           class="btn btn-danger">Sim, excluir</a>
      </div>

    </div>
  </div>
</div>

<script>
// Quando o modal abre, preenche nome e URL de exclusão
const modalEl = document.getElementById('modalExcluir');
modalEl.addEventListener('show.bs.modal', function(event) {
    const btn  = event.relatedTarget; // botão que abriu o modal
    const id   = btn.getAttribute('data-id');
    const nome = btn.getAttribute('data-nome');

    document.getElementById('modalNome').textContent = nome;
    document.getElementById('btnConfirmarExcluir').href =
        'excluir.php?id=' + id;
});
</script>