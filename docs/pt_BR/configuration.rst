Configuração
============

O plugin funciona sem configuração. O administrador pode manter a lista de exemplos de CSS oferecidos no editor de áreas.

Exemplos de CSS
---------------

Acesse *Administração do site > Plugins > Módulos de atividade > Image Map* e siga **Manage CSS Examples**. Cada exemplo tem nome, tipo e código CSS:

* **CSS ativo** e **CSS inativo**: o estilo de uma área cujo destino está ou não disponível.
* **CSS ativo (hover)** e **CSS inativo (hover)**: o estilo enquanto o ponteiro está sobre a área.

A instalação cria um conjunto de exemplos padrão de cada tipo (por exemplo *No effect*, *Grayscale* e *Blue border*); a atualização a partir da 1.3.1 ou anterior troca os nomes antigos em português pelos nomes em inglês e cria os padrões dos tipos que não têm nenhum exemplo. Depois disso, a lista só muda pelo administrador.

Cada cartão mostra uma prévia do CSS sobre fundo escuro e claro. Os exemplos podem ser exportados para um arquivo JSON e importados em outro site; um exemplo importado com o mesmo tipo e nome substitui o existente.

CSS permitido
-------------

O CSS das áreas é limpo no servidor ao ser salvo, importado ou restaurado. Só estas propriedades são mantidas: ``filter``, ``opacity``, ``transform``, ``transition``, ``border`` (e ``border-color``, ``-width``, ``-style``, ``-radius``), ``outline`` (e ``outline-color``, ``-width``, ``-style``, ``-offset``), ``box-shadow``, ``background-color`` e ``mix-blend-mode``. Os valores podem usar letras, números, ``#``, ``%``, ``.``, ``,``, parênteses, ``+`` e ``-``; qualquer coisa com ``url()``, ``expression()``, aspas, chaves ou comentários é descartada. Um filtro sem propriedade, como ``grayscale(1)``, vira ``filter: grayscale(1)``.

Permissões
----------

* ``mod/imagemap:addinstance``: adicionar um mapa de imagem ao curso (professores e gerentes).
* ``mod/imagemap:view``: ver o mapa de imagem.
* ``mod/imagemap:manage``: editar áreas e linhas (professores e gerentes). Quem tem essa permissão vê todas as áreas como ativas.
