Backup e restauração
====================

Os mapas de imagem entram nos backups de curso, nas importações e na duplicação de atividades, com a imagem, os arquivos da descrição, as áreas e as linhas.

* Ao restaurar o curso inteiro, cada área aponta para a cópia da sua atividade ou seção no novo curso, qualquer que seja a ordem delas no curso.
* Ao restaurar ou duplicar só o mapa de imagem, as áreas que apontam para itens do mesmo curso os mantêm; as que apontam para itens fora do backup perdem o destino e precisam ser ligadas de novo.
* O CSS das áreas é limpo de novo na restauração (veja :doc:`configuration`).
* Os exemplos de CSS são configuração do site: não entram nos backups e a restauração nunca os altera. Cada área guarda o seu próprio CSS, então um mapa de imagem restaurado fica igual sem eles. Para copiar os exemplos para outro site, use a exportação e a importação de exemplos (veja :doc:`configuration`).

Privacidade
-----------

O plugin não guarda dados pessoais: áreas, linhas e exemplos de CSS são conteúdo do curso e do site. Ele implementa a Privacy API como null provider.
