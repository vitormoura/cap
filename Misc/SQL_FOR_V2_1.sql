use intranet;
alter table documento drop column codPlano;
create table PlanoDeContas ( codPlano int(5) primary key, plano varchar(30) not null );
alter table pagamento add column codPlano int(5) not null default 20100 after codDoc;
exit;