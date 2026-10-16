-- TechLoja: catálogo fictício para demonstração.
-- Execute no banco MySQL defaultdb da Aiven DEPOIS de remover a coluna validade.
-- Preços e estoques são ilustrativos; não representam ofertas reais.
-- Evita inserir novamente os itens com o mesmo nome.
START TRANSACTION;
INSERT INTO produto (nome, valor, quantidade)
SELECT novos.nome, novos.valor, novos.quantidade
FROM (
 SELECT 'Processador AMD Ryzen 5 5600X' AS nome, 899.90 AS valor, 12 AS quantidade
 UNION ALL SELECT 'Placa de video NVIDIA GeForce RTX 4060 8GB', 1999.90, 8
 UNION ALL SELECT 'Memoria RAM Kingston Fury Beast 16GB DDR4', 289.90, 30
 UNION ALL SELECT 'SSD NVMe Kingston NV2 1TB', 399.90, 24
 UNION ALL SELECT 'Monitor Gamer 24 polegadas 165Hz Full HD', 849.90, 10
 UNION ALL SELECT 'Teclado Mecanico RGB ABNT2', 249.90, 18
 UNION ALL SELECT 'Mouse Gamer Sem Fio 26000 DPI', 219.90, 20
 UNION ALL SELECT 'Headset Gamer USB com Microfone', 189.90, 14
 UNION ALL SELECT 'Fonte ATX 650W 80 Plus Bronze', 379.90, 12
 UNION ALL SELECT 'Placa Mae B550M AM4', 649.90, 9
 UNION ALL SELECT 'Gabinete Gamer Mid Tower com 3 Fans', 329.90, 11
 UNION ALL SELECT 'Webcam Full HD 1080p USB', 159.90, 25
 UNION ALL SELECT 'Notebook 15.6 polegadas Ryzen 5 8GB RAM 512GB SSD', 2999.90, 5
 UNION ALL SELECT 'Roteador Wi-Fi 6 Dual Band AX1800', 299.90, 15
 UNION ALL SELECT 'Controle Bluetooth para PC', 179.90, 17
) AS novos
WHERE NOT EXISTS (SELECT 1 FROM produto p WHERE p.nome = novos.nome);
COMMIT;
