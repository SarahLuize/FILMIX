function debounce(funcao, espera) { /*https://medium.com/@vemlavaralouca/debounce-javascript-b4c99ec4b13f*/
    let timeoutId;

    return function () {
        const context = this;
        const args = arguments;
        if (timeoutId) clearTimeout(timeoutId);
        timeoutId = setTimeout(function () {
            funcao.apply(context, args);
        }, espera);
    };
}

function autocomplete(campoPesquisa, listaSugestoes) {
    /*A função de autocompletar recebe dois argumentos:
    o campo de pesquisa e uma lista de possíveis sugestões de filmes*/
    /* Código alterado do https://www.w3schools.com/howto/howto_js_autocomplete.asp*/
    console.log("Função autocomplete iniciada");
    console.log("Renderizando", listaSugestoes);
    // console.log(sugestoesDoTMDB);
    var focoAtual;
    
    function renderizarLista(listaSugestoes, valorDigitado){
        var containerSugestoes, sugestao, i;
        /*fecha todas as listas de sugestões já abertas*/
        fecharTodasListas();
        if (!valorDigitado) { return false; }
        focoAtual = -1;
        /*cria um elemento DIV que armazenará as sugestões*/
        containerSugestoes = document.createElement("DIV");
        containerSugestoes.setAttribute("id", campoPesquisa.id + "autocomplete-list");
        containerSugestoes.setAttribute("class", "autocomplete-items");
        /*adiciona o elemento DIV como filho do contêiner de autocompletar*/
        campoPesquisa.parentNode.appendChild(containerSugestoes);
        for (i = 0; i < 10; i++) {
            /*cria um elemento DIV para cada sugestão correspondente:*/
            sugestao = document.createElement("DIV");
            /*verifica se a sugestão começa com as mesmas letras digitadas pelo usuário*/
            if(listaSugestoes[i].substr(0, valorDigitado.length).toUpperCase()==valorDigitado.toUpperCase()){
                /*destaca em negrito as letras correspondentes*/
                sugestao.innerHTML = "<strong>" + listaSugestoes[i].substr(0, valorDigitado.length) + "</strong>";
                sugestao.innerHTML += listaSugestoes[i].substr(valorDigitado.length);
            } else {
                /*se não começar igual, mostra o título enviado pelo TMDB*/
                sugestao.innerHTML = listaSugestoes[i];
            }
                
            /*insere um campo oculto que armazenará o valor da sugestão atual:*/
            sugestao.innerHTML += "<input type='hidden' value='" + listaSugestoes[i].replace(/'/g, "&apos;") + "'>";
            /*executa uma função quando o usuário clicar em uma sugestão*/
            sugestao.addEventListener("click", function (e) {
                /*insere a sugestão selecionada no campo de pesquisa*/
                campoPesquisa.value = this.getElementsByTagName("input")[0].value;
                /*fecha a lista de sugestões abertas:*/
                fecharTodasListas();
            });
            containerSugestoes.appendChild(sugestao);
        }
    }

        // chama PHP usando Debounce conferir código do gemini
        campoPesquisa.addEventListener("input", debounce(function(e){
            const texto = e.target.value;
            if(texto.length < 3){
                fecharTodasListas();
                return;
            }
            //https://www.w3schools.com/php/php_ajax_php.asp
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    sugestoesDoTMDB = JSON.parse(this.responseText);
                    console.log(sugestoesDoTMDB);
                    renderizarLista(sugestoesDoTMDB, texto);
                }
            };

            xmlhttp.open("GET", "buscar_filmes.php?s=" + encodeURIComponent(texto));
            xmlhttp.send();

        },300 ));

    /*executa uma função quando uma tecla do teclado é pressionada*/
    campoPesquisa.addEventListener("keydown", function (e) {
        var listaItens = document.getElementById(this.id + "autocomplete-list");
        if (listaItens) listaItens = listaItens.getElementsByTagName("div");
        if (e.keyCode == 40) {
            /*se a tecla de seta para baixo for pressionada, aumenta a variável focoAtual:*/
            focoAtual++;
            /*destaca visualmente o item atual.*/
            adicionarAtivo(listaItens);
        } else if (e.keyCode == 38) { //tecla para cima
            /*se a tecla de seta para cima for pressionada, diminui a variável focoAtual:*/
            focoAtual--;
            /*destaca visualmente o item atual.*/
            adicionarAtivo(listaItens);
        } else if (e.keyCode == 13) { // tecla ENTER
            /*se a tecla ENTER for pressionada, impede que o formulário seja enviado,*/
            // e.preventDefault();
            if (focoAtual > -1) {
                e.preventDefault();
                /*simula um clique no item atual*/
                if (listaItens) listaItens[focoAtual].click();
            }
        }
    });

    function adicionarAtivo(listaItens) {
        /*adiciona o estado ativo ao item selecionado.*/
        if (!listaItens) return false;
        /*remove a classe ativa de todos os itens*/
        removerAtivo(listaItens);
        if (focoAtual >= listaItens.length) focoAtual = 0;
        if (focoAtual < 0) focoAtual = (listaItens.length - 1);
        /*adiciona a classe "autocomplete-active" ao item atual*/
        listaItens[focoAtual].classList.add("autocomplete-active");
    }

    function removerAtivo(listaItens) {
        /*remove a classe ativa de todos os itens*/
        for (var i = 0; i < listaItens.length; i++) {
            listaItens[i].classList.remove("autocomplete-active");
        }
    }

    function fecharTodasListas(elemento) {
        /*fecha todas as listas de sugestões, exceto a passada como argumento*/
        var listaItens = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < listaItens.length; i++) {
            if (elemento != listaItens[i] && elemento != campoPesquisa) {
                listaItens[i].parentNode.removeChild(listaItens[i]);
            }
        }
    }
    /*executa uma função quando o usuário clicar em qualquer lugar do documento*/
    document.addEventListener("click", function (e) {
        fecharTodasListas(e.target);
    });
};