const tabela       = document.getElementById('tabela-alunos');
const info         = document.getElementById('info');   // não use "status": já existe em window
const paginacao    = document.getElementById('paginacao');
const inputBusca   = document.getElementById('busca');
const selectTurma  = document.getElementById('turma');
const modalEl      = document.getElementById('modalExcluir');
const modalNome    = document.getElementById('excluir-nome');
const btnConfirmar = document.getElementById('btn-confirmar-excluir');
const toastEl      = document.getElementById('toast');
const toastMsg     = document.getElementById('toast-msg');

const modal = new bootstrap.Modal(modalEl);
const toast = new bootstrap.Toast(toastEl, { delay: 3000 });

let pagina = 1;
let idParaExcluir = null;
let controlador = null;   // AbortController da requisição atual
let temporizador;         // debounce da busca

// "Ana Silva" => "AS" (avatar quando não há foto)
function iniciais(nome) {
  const partes = nome.trim().split(/\s+/).filter(Boolean);
  if (!partes.length) return '?';
  let ini = partes[0][0];
  ini += partes.length > 1 ? partes[partes.length - 1][0] : (partes[0][1] || '');
  return ini.toUpperCase();
}

function mostrarToast(texto, ok = true) {
  toastEl.className = 'toast align-items-center border-0 text-bg-' + (ok ? 'success' : 'danger');
  toastMsg.textContent = texto;
  toast.show();
}

async function carregarAlunos() {
  // cancela a requisição anterior, se ainda estiver rodando
  if (controlador) controlador.abort();
  controlador = new AbortController();

  const params = new URLSearchParams({
    busca: inputBusca.value.trim(),
    turma: selectTurma.value,
    pagina: pagina,
  });

  info.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Carregando...';

  try {
    const resposta = await fetch('api/alunos_listar.php?' + params, { signal: controlador.signal });

    if (resposta.status === 401) {          // sessão expirou
      window.location = 'login.php';
      return;
    }
    if (!resposta.ok) throw new Error('Erro ' + resposta.status);

    const dados = await resposta.json();
    pagina = dados.pagina;

    desenharTabela(dados.alunos);
    desenharPaginacao(dados.pagina, dados.totalPaginas);
    info.textContent = `Resultados encontrados: ${dados.total}`;

  } catch (erro) {
    if (erro.name === 'AbortError') return;   // cancelada de propósito
    console.error('Falha ao carregar alunos:', erro);
    info.textContent = 'Não foi possível carregar os alunos. Tente novamente.';
  }
}

function celula(texto, classe) {
  const td = document.createElement('td');
  td.textContent = texto ?? '';            // textContent = seguro contra XSS
  if (classe) td.className = classe;
  return td;
}

function desenharTabela(alunos) {
  tabela.innerHTML = '';

  if (alunos.length === 0) {
    const tr = document.createElement('tr');
    const td = celula('Nenhum aluno encontrado.', 'text-center text-muted py-4');
    td.colSpan = 6;
    tr.appendChild(td);
    tabela.appendChild(tr);
    return;
  }

  for (const a of alunos) {
    const tr = document.createElement('tr');
    tr.appendChild(celula(a.id, 'text-muted'));

    // foto ou avatar com iniciais
    const tdFoto = document.createElement('td');
    if (a.foto) {
      const img = document.createElement('img');
      img.src = a.foto;
      img.alt = 'Foto de ' + a.nome;
      img.className = 'foto-aluno';
      img.loading = 'lazy';
      tdFoto.appendChild(img);
    } else {
      const span = document.createElement('span');
      span.className = 'avatar-aluno';
      span.textContent = iniciais(a.nome);
      tdFoto.appendChild(span);
    }
    tr.appendChild(tdFoto);

    tr.appendChild(celula(a.nome, 'fw-semibold'));
    tr.appendChild(celula(a.email));
    tr.appendChild(celula(a.turma));

    // ações: addEventListener em vez de onclick="..." no HTML
    const tdAcoes = document.createElement('td');
    tdAcoes.className = 'text-nowrap';

    const editar = document.createElement('a');
    editar.href = 'editar.php?id=' + encodeURIComponent(a.id);
    editar.className = 'btn btn-outline-primary btn-sm me-1';
    editar.textContent = '✏️ Editar';

    const excluir = document.createElement('button');
    excluir.type = 'button';
    excluir.className = 'btn btn-outline-danger btn-sm';
    excluir.textContent = '🗑️ Excluir';
    excluir.addEventListener('click', () => {
      idParaExcluir = a.id;
      modalNome.textContent = a.nome;
      modal.show();
    });

    tdAcoes.append(editar, excluir);
    tr.appendChild(tdAcoes);
    tabela.appendChild(tr);
  }
}

function desenharPaginacao(atual, total) {
  paginacao.innerHTML = '';
  if (total <= 1) return;

  const item = (rotulo, destino, desabilitado = false, ativo = false) => {
    const li = document.createElement('li');
    li.className = 'page-item' + (desabilitado ? ' disabled' : '') + (ativo ? ' active' : '');
    const a = document.createElement('a');
    a.className = 'page-link';
    a.href = '#';
    a.textContent = rotulo;
    a.addEventListener('click', (e) => {
      e.preventDefault();
      if (desabilitado || ativo) return;
      pagina = destino;
      carregarAlunos();
    });
    li.appendChild(a);
    paginacao.appendChild(li);
  };

  item('Anterior', atual - 1, atual <= 1);
  for (let p = 1; p <= total; p++) item(String(p), p, false, p === atual);
  item('Próxima', atual + 1, atual >= total);
}

// ---------- eventos ----------

// busca ao vivo com debounce: só busca 300 ms depois da última tecla
inputBusca.addEventListener('input', () => {
  clearTimeout(temporizador);
  temporizador = setTimeout(() => {
    pagina = 1;
    carregarAlunos();
  }, 300);
});

selectTurma.addEventListener('change', () => {
  pagina = 1;
  carregarAlunos();
});

document.getElementById('form-busca').addEventListener('submit', (e) => {
  e.preventDefault();
  pagina = 1;
  carregarAlunos();
});

document.getElementById('btn-limpar').addEventListener('click', () => {
  inputBusca.value = '';
  selectTurma.value = '';
  pagina = 1;
  carregarAlunos();
});

// exclusão: POST com corpo JSON
btnConfirmar.addEventListener('click', async () => {
  btnConfirmar.disabled = true;
  try {
    const resposta = await fetch('api/aluno_excluir.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: idParaExcluir }),
    });

    if (resposta.status === 401) {
      window.location = 'login.php';
      return;
    }

    const dados = await resposta.json();
    modal.hide();

    if (resposta.ok && dados.ok) {
      mostrarToast('Aluno excluído com sucesso!');
      carregarAlunos();                    // atualiza só a tabela
    } else {
      mostrarToast(dados.erro || 'Erro ao excluir.', false);
    }
  } catch (erro) {
    modal.hide();
    mostrarToast('Falha de conexão. Tente novamente.', false);
  } finally {
    btnConfirmar.disabled = false;
  }
});

carregarAlunos();   // carga inicial
