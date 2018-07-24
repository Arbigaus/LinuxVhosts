Script desenvolvido pelo Luciano Charles para criação de Virtual Hosts no Windows e adaptado para Linux.

Como funciona:

1 - Funciona através do browser, utilizando o próprio PHP para criação dos Virtuais Hosts.

2 - Para o funcionamento correto, o usuário e grupo www-data necessita de permissão de escrita nas pastas e documentos abaixo:
	- /etc/apache2/sites-available
	- /etc/apache2/sites-enabled
	- /var/www/
	- /etc/hosts

	* Você pode deixar o grupo www-data como dono das pastas: 
		- sudo chown -R :www-data *folder* ou *arquivo*

	* Adicione teu usuário padrão no grupo :www-data ( sudo usermod -aG www-data username )

3 - Para que ativação do novo Virtual Host funcione corretamente, é necessário dar permissão para que o usuário www-data possa utilizar o comando sudo sem necessidade de senha.
	- Para remover a solicitação de senha do usuário no comando sudo, insira a linha abaixo no arquivo /etc/sudoers:

	* www-data ALL=(ALL) NOPASSWD: ALL

Se tudo estiver corretamente, irá ser criado o virtual host com o nome-do-host.com.br e o mesmo já estará incluso no arquivo host, basta acessar via browser o novo host.