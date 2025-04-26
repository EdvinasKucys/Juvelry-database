-- INSERT duomenys į juvelyrika_smol duomenų bazę
USE juvelyrika_smol;

-- Išvalome esamus duomenis (jei reikia)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE sandeliuojama_preke;
TRUNCATE TABLE preke;
TRUNCATE TABLE gamintojas;
TRUNCATE TABLE kategorija;
SET FOREIGN_KEY_CHECKS = 1;

-- Kategorijos (10 įrašų)
INSERT INTO kategorija (pavadinimas, aprasymas) VALUES 
('Žiedai', 'Įvairių tipų juvelyriniai žiedai skirtingoms progoms'),
('Vėriniai', 'Elegantiški vėriniai kasdieniam dėvėjimui ir ypatingoms progoms'),
('Apyrankės', 'Stilingos apyrankės, kurios papildo bet kurį aprangos derinį'),
('Auskarai', 'Gražūs auskarai nuo sagučių iki kabančių modelių'),
('Laikrodžiai', 'Aukščiausios kokybės laikrodžiai vyrams ir moterims'),
('Pakabukai', 'Unikalūs pakabukai jūsų vėriniams personalizuoti'),
('Sagės', 'Klasikinės ir modernios sagės bet kuriai progai'),
('Kojos papuošalai', 'Subtilūs kojos papuošalai kasdieniam ir iškilmingam dėvėjimui'),
('Vestuviniai papuošalai', 'Specialūs papuošalai vestuvių ceremonijai'),
('Rinkiniai', 'Suderinti juvelyriniai rinkiniai vieningam įvaizdžiui');

-- Gamintojai (10 įrašų)
INSERT INTO gamintojas (gamintojo_id, pavadinimas, salis, kontaktai) VALUES 
('TIFF001', 'Tiffany & Co.', 'JAV', 'contact@tiffany.com, +1-212-555-0101'),
('CART002', 'Cartier', 'Prancūzija', 'info@cartier.com, +33-1-4455-3322'),
('BVLG003', 'Bulgari', 'Italija', 'support@bulgari.com, +39-06-8888-7766'),
('PAND004', 'Pandora', 'Danija', 'service@pandora.net, +45-3333-2211'),
('SWRV005', 'Swarovski', 'Austrija', 'crystal@swarovski.com, +43-5224-5000'),
('HRMS006', 'Hermès Jewelry', 'Prancūzija', 'jewelry@hermes.com, +33-1-4017-4717'),
('GCCI007', 'Gucci', 'Italija', 'jewels@gucci.com, +39-055-7592-7010'),
('AMYR008', 'Gintaro papuošalai', 'Lietuva', 'info@gintaropapuosalai.lt, +370-5-212-1212'),
('BCVR009', 'Baltijos amatai', 'Latvija', 'sales@balticrafts.lv, +371-67-223344'),
('LTHR010', 'Lietuviškas paveldas', 'Lietuva', 'orders@ltpaveldas.lt, +370-5-111-2222');

-- Prekės (60 įrašų)
INSERT INTO preke (id, pavadinimas, aprasymas, kaina, svoris, medziaga, fk_GAMINTOJASgamintojo_id, fk_KATEGORIJAid_KATEGORIJA) VALUES 
-- Žiedai
('PROD0001', 'Deimantinis aukso žiedas', 'Elegantiškas deimantinis žiedas iš aukštos kokybės aukso.', 1299.99, 5.2, 'Auksas', 'TIFF001', 1),
('PROD0002', 'Sidabrinis sužadėtuvių žiedas', 'Klasikinis sidabrinis žiedas su mažu deimantu.', 499.99, 4.5, 'Sidabras', 'CART002', 1),
('PROD0003', 'Platinos vestuviniai žiedai (pora)', 'Aukščiausios kokybės platinos vestuvinių žiedų pora.', 1899.99, 12.0, 'Platina', 'BVLG003', 1),
('PROD0004', 'Gintarinis žiedas', 'Tradicinis žiedas su gintaro akmeniu.', 199.99, 3.8, 'Sidabras, Gintaras', 'AMYR008', 1),
('PROD0005', 'Rubino žiedas', 'Prabangus žiedas su rubino akmeniu.', 899.99, 4.2, 'Baltas auksas', 'CART002', 1),
('PROD0006', 'Minimalistinis žiedas', 'Modernaus dizaino minimalistinis žiedas.', 149.99, 2.5, 'Sidabras', 'PAND004', 1),

-- Vėriniai
('PROD0007', 'Perlų vėrinys', 'Elegantiškas perlų vėrinys su sidabrine užsegimo sistema.', 299.99, 18.5, 'Perlai, Sidabras', 'PAND004', 2),
('PROD0008', 'Aukso grandinėlė', 'Klasikinio dizaino aukso grandinėlė.', 699.99, 12.3, 'Auksas', 'SWRV005', 2),
('PROD0009', 'Gintaro karoliai', 'Tradiciniai gintaro karoliai su unikaliais natūraliais gintaro akmenimis.', 149.99, 22.0, 'Gintaras', 'AMYR008', 2),
('PROD0010', 'Sidabrinis vėrinys su safyru', 'Elegantiškas sidabrinis vėrinys su safyro akmeniu.', 249.99, 15.0, 'Sidabras, Safyras', 'BVLG003', 2),
('PROD0011', 'Chokeris su kristalais', 'Modernus chokerio stiliaus vėrinys su Swarovski kristalais.', 179.99, 14.0, 'Oda, Kristalai', 'SWRV005', 2),
('PROD0012', 'Ilgas perlų vėrinys', 'Klasikinis ilgas perlų vėrinys.', 349.99, 35.0, 'Perlai', 'TIFF001', 2),

-- Apyrankės
('PROD0013', 'Odinė apyrankė vyrams', 'Stilinga odinė apyrankė su nerūdijančio plieno elementais.', 89.99, 15.0, 'Oda, Plienas', 'HRMS006', 3),
('PROD0014', 'Sidabrinė tenisinė apyrankė', 'Klasikinė sidabrinė tenisinė apyrankė su cirkoniais.', 199.99, 14.2, 'Sidabras', 'GCCI007', 3),
('PROD0015', 'Gintaro apyrankė', 'Moderni apyrankė su gintaro intarpais.', 129.99, 16.5, 'Gintaras, Sidabras', 'AMYR008', 3),
('PROD0016', 'Auksinė grandinė apyrankė', 'Elegantiška auksinė grandinės apyrankė.', 599.99, 18.0, 'Auksas', 'CART002', 3),
('PROD0017', 'Charm apyrankė', 'Sidabrinė apyrankė su keičiamais pakabutėliais.', 149.99, 17.5, 'Sidabras', 'PAND004', 3),
('PROD0018', 'Sportinė apyrankė', 'Titaninė apyrankė aktyviam gyvenimo būdui.', 129.99, 12.0, 'Titanas', 'SWRV005', 3),

-- Auskarai
('PROD0019', 'Deimantiniai sagučiai', 'Klasikiniai deimanto sagučiai.', 899.99, 1.8, 'Baltas auksas', 'TIFF001', 4),
('PROD0020', 'Sidabriniai kabantys auskarai', 'Elegantiški kabantys auskarai su cirkoniais.', 149.99, 3.2, 'Sidabras', 'CART002', 4),
('PROD0021', 'Gintaro auskarai', 'Tradiciniai gintaro auskarai su sidabriniais elementais.', 79.99, 2.5, 'Gintaras, Sidabras', 'AMYR008', 4),
('PROD0022', 'Perlų auskarai', 'Klasikiniai perlų sagučiai.', 129.99, 2.0, 'Perlai, Sidabras', 'PAND004', 4),
('PROD0023', 'Chandelier auskarai', 'Prabangūs ilgi chandelier stiliaus auskarai.', 199.99, 5.0, 'Sidabras, Kristalai', 'BVLG003', 4),
('PROD0024', 'Minimalistiniai auskarai', 'Modernaus dizaino geometriniai auskarai.', 69.99, 1.5, 'Sidabras', 'HRMS006', 4),

-- Laikrodžiai
('PROD0025', 'Vyriškas sportinis laikrodis', 'Aukštos kokybės sportinis laikrodis su daugybe funkcijų.', 299.99, 85.0, 'Nerūdijantis plienas', 'SWRV005', 5),
('PROD0026', 'Moteriškas prabangus laikrodis', 'Elegantiškas moteriškas laikrodis su deimantais.', 1999.99, 45.0, 'Rožinis auksas', 'CART002', 5),
('PROD0027', 'Unisex minimalistinis laikrodis', 'Modernus, minimalistinio dizaino laikrodis.', 199.99, 52.0, 'Titanas', 'BVLG003', 5),
('PROD0028', 'Išmanusis laikrodis', 'Aukštos kokybės išmanusis laikrodis su visomis funkcijomis.', 399.99, 48.0, 'Aliuminis', 'TIFF001', 5),
('PROD0029', 'Vintažinis laikrodis', 'Klasikinio dizaino vintažinis laikrodis.', 299.99, 60.0, 'Žalvaris', 'BCVR009', 5),
('PROD0030', 'Kišeninis laikrodis', 'Elegantiškas kišeninis laikrodis su grandinėle.', 259.99, 75.0, 'Sidabras', 'LTHR010', 5),

-- Pakabukai
('PROD0031', 'Širdies formos pakabukas', 'Romantiškas širdies formos pakabukas su cirkoniais.', 89.99, 3.5, 'Sidabras', 'PAND004', 6),
('PROD0032', 'Gintarinis pakabukas su vabzdžiu', 'Unikalus gintaro pakabukas su suakmenėjusiu vabzdžiu.', 199.99, 5.2, 'Gintaras, Sidabras', 'AMYR008', 6),
('PROD0033', 'Kryžiaus formos pakabukas', 'Tradicinis kryžiaus formos pakabukas.', 129.99, 4.0, 'Auksas', 'TIFF001', 6),
('PROD0034', 'Inicialų pakabukas', 'Personalizuotas pakabukas su inicialais.', 99.99, 3.0, 'Sidabras', 'CART002', 6),
('PROD0035', 'Zodiako ženklo pakabukas', 'Pakabukas su zodiako ženklo simboliu.', 79.99, 2.8, 'Sidabras', 'SWRV005', 6),
('PROD0036', 'Geometrinis pakabukas', 'Modernaus dizaino geometrinis pakabukas.', 69.99, 2.5, 'Nerūdijantis plienas', 'GCCI007', 6),

-- Sagės
('PROD0037', 'Gėlės formos sagė', 'Elegantiška gėlės formos sagė su spalvotais akmenimis.', 149.99, 8.5, 'Sidabras', 'BCVR009', 7),
('PROD0038', 'Vintage stiliaus sagė', 'Klasikinė vintage stiliaus sagė su perlais.', 199.99, 10.2, 'Sidabras, Perlai', 'LTHR010', 7),
('PROD0039', 'Modernistinė geometrinė sagė', 'Šiuolaikinė geometrinių formų sagė.', 179.99, 7.8, 'Nerūdijantis plienas', 'GCCI007', 7),
('PROD0040', 'Drugelio sagė', 'Detaliai išdirbta drugelio formos sagė.', 159.99, 9.0, 'Sidabras, Emalė', 'BVLG003', 7),
('PROD0041', 'Art deco sagė', 'Elegantiška art deco periodo stiliaus sagė.', 229.99, 12.5, 'Sidabras, Onikso', 'CART002', 7),
('PROD0042', 'Gyvūno formos sagė', 'Žaisminga gyvūno formos sagė.', 139.99, 8.2, 'Sidabras, Emalė', 'PAND004', 7),

-- Kojos papuošalai
('PROD0043', 'Sidabrinė kojos grandinėlė', 'Subtili sidabrinė kojos grandinėlė.', 69.99, 5.2, 'Sidabras', 'PAND004', 8),
('PROD0044', 'Kojos apyrankė su pakabutėliais', 'Linksma kojos apyrankė su įvairiais pakabutėliais.', 89.99, 7.5, 'Sidabras', 'SWRV005', 8),
('PROD0045', 'Perlų kojos papuošalas', 'Elegantiškas kojos papuošalas su perlais.', 99.99, 6.3, 'Perlai, Sidabras', 'BCVR009', 8),
('PROD0046', 'Vasaros kojos grandinėlė', 'Lengva kojos grandinėlė vasaros sezonui.', 59.99, 4.5, 'Sidabras', 'AMYR008', 8),
('PROD0047', 'Paplūdimio kojos papuošalas', 'Spalvingas kojos papuošalas paplūdimiui.', 49.99, 5.0, 'Sidabras, Spalvoti akmenys', 'LTHR010', 8),
('PROD0048', 'Boho stiliaus kojos papuošalas', 'Daugiasluoksnis boho stiliaus kojos papuošalas.', 79.99, 8.0, 'Sidabras, Oda', 'HRMS006', 8),

-- Vestuviniai papuošalai
('PROD0049', 'Vestuvinė tiara', 'Prabangaus dizaino vestuvinė tiara su kristalais.', 399.99, 120.0, 'Sidabras, Kristalai', 'TIFF001', 9),
('PROD0050', 'Vestuvių žiedų pagalvėlė', 'Rankomis siuvinėta vestuvių žiedų pagalvėlė.', 59.99, 80.0, 'Šilkas, Satinas', 'LTHR010', 9),
('PROD0051', 'Vestuvinė apyrankė nuotakai', 'Subtili apyrankė nuotakai su kristalais.', 149.99, 8.5, 'Sidabras, Kristalai', 'CART002', 9),
('PROD0052', 'Vestuviniai plaukų papuošalai', 'Elegantiški plaukų papuošalai nuotakai.', 129.99, 15.0, 'Sidabras, Perlai', 'SWRV005', 9),
('PROD0053', 'Jaunikio sagė', 'Stilinga sagė jaunikiui.', 89.99, 6.0, 'Sidabras', 'GCCI007', 9),
('PROD0054', 'Nuotakos vualio segė', 'Subtili segė nuotakos vualį pritvirtinti.', 79.99, 4.0, 'Sidabras, Kristalai', 'BVLG003', 9),

-- Rinkiniai
('PROD0055', 'Perlų rinkinys (vėrinys ir auskarai)', 'Elegantiškas perlų vėrinys su derančiais auskarais.', 399.99, 25.0, 'Perlai, Sidabras', 'BVLG003', 10),
('PROD0056', 'Gintaro rinkinys', 'Trijų dalių gintaro rinkinys - vėrinys, auskarai ir žiedas.', 349.99, 32.5, 'Gintaras, Sidabras', 'AMYR008', 10),
('PROD0057', 'Deimantų rinkinys ypatingai progai', 'Prabangus rinkinys ypatingoms progoms su deimantais.', 2999.99, 18.7, 'Baltas auksas, Deimantai', 'TIFF001', 10),
('PROD0058', 'Vestuvių papuošalų rinkinys', 'Pilnas vestuvinis papuošalų rinkinys nuotakai.', 799.99, 45.0, 'Sidabras, Perlai, Kristalai', 'CART002', 10),
('PROD0059', 'Verslo stiliaus rinkinys', 'Elegantiškas rinkinys verslui - sagė ir auskarai.', 299.99, 14.0, 'Sidabras', 'HRMS006', 10),
('PROD0060', 'Kasdieninio dėvėjimo rinkinys', 'Minimalistinis rinkinys kasdienai.', 199.99, 12.0, 'Sidabras', 'PAND004', 10);

-- Sandeliuojama_preke (140 įrašų - po kelis kiekvienai prekei)
-- Žiedai - įvairios atsargos skirtingose vietose ar skirtingų dydžių
INSERT INTO sandeliuojama_preke (kiekis, fk_PREKEid) VALUES 
-- PROD0001-0006 (Žiedai)
(5, 'PROD0001'), (3, 'PROD0001'), (2, 'PROD0001'),
(10, 'PROD0002'), (7, 'PROD0002'), (3, 'PROD0002'),
(2, 'PROD0003'), (1, 'PROD0003'),
(12, 'PROD0004'), (8, 'PROD0004'),
(6, 'PROD0005'), (4, 'PROD0005'),
(15, 'PROD0006'), (10, 'PROD0006'),

-- PROD0007-0012 (Vėriniai)
(8, 'PROD0007'), (5, 'PROD0007'),
(12, 'PROD0008'), (0, 'PROD0008'),
(15, 'PROD0009'), (9, 'PROD0009'),
(7, 'PROD0010'), (3, 'PROD0010'),
(11, 'PROD0011'), (6, 'PROD0011'),
(4, 'PROD0012'), (2, 'PROD0012'),

-- PROD0013-0018 (Apyrankės)
(20, 'PROD0013'), (8, 'PROD0013'),
(6, 'PROD0014'), (4, 'PROD0014'),
(9, 'PROD0015'), (7, 'PROD0015'),
(5, 'PROD0016'), (3, 'PROD0016'),
(14, 'PROD0017'), (8, 'PROD0017'),
(12, 'PROD0018'), (9, 'PROD0018'),

-- PROD0019-0024 (Auskarai)
(15, 'PROD0019'), (12, 'PROD0019'),
(25, 'PROD0020'), (0, 'PROD0020'),
(18, 'PROD0021'), (13, 'PROD0021'),
(10, 'PROD0022'), (5, 'PROD0022'),
(8, 'PROD0023'), (4, 'PROD0023'),
(22, 'PROD0024'), (16, 'PROD0024'),

-- PROD0025-0030 (Laikrodžiai)
(5, 'PROD0025'), (3, 'PROD0025'),
(7, 'PROD0026'), (2, 'PROD0026'),
(10, 'PROD0027'), (6, 'PROD0027'),
(8, 'PROD0028'), (5, 'PROD0028'),
(4, 'PROD0029'), (2, 'PROD0029'),
(6, 'PROD0030'), (3, 'PROD0030'),

-- PROD0031-0036 (Pakabukai)
(30, 'PROD0031'), (15, 'PROD0031'),
(8, 'PROD0032'), (5, 'PROD0032'),
(12, 'PROD0033'), (8, 'PROD0033'),
(18, 'PROD0034'), (10, 'PROD0034'),
(25, 'PROD0035'), (15, 'PROD0035'),
(20, 'PROD0036'), (12, 'PROD0036'),

-- PROD0037-0042 (Sagės)
(7, 'PROD0037'), (4, 'PROD0037'),
(9, 'PROD0038'), (6, 'PROD0038'),
(11, 'PROD0039'), (7, 'PROD0039'),
(5, 'PROD0040'), (3, 'PROD0040'),
(8, 'PROD0041'), (5, 'PROD0041'),
(12, 'PROD0042'), (8, 'PROD0042'),

-- PROD0043-0048 (Kojos papuošalai)
(14, 'PROD0043'), (8, 'PROD0043'),
(11, 'PROD0044'), (7, 'PROD0044'),
(9, 'PROD0045'), (5, 'PROD0045'),
(16, 'PROD0046'), (10, 'PROD0046'),
(20, 'PROD0047'), (15, 'PROD0047'),
(13, 'PROD0048'), (7, 'PROD0048'),

-- PROD0049-0054 (Vestuviniai papuošalai)
(3, 'PROD0049'), (2, 'PROD0049'),
(15, 'PROD0050'), (10, 'PROD0050'),
(8, 'PROD0051'), (5, 'PROD0051'),
(6, 'PROD0052'), (4, 'PROD0052'),
(9, 'PROD0053'), (6, 'PROD0053'),
(12, 'PROD0054'), (8, 'PROD0054'),

-- PROD0055-0060 (Rinkiniai)
(5, 'PROD0055'), (3, 'PROD0055'),
(7, 'PROD0056'), (4, 'PROD0056'),
(2, 'PROD0057'), (1, 'PROD0057'),
(6, 'PROD0058'), (3, 'PROD0058'),
(8, 'PROD0059'), (4, 'PROD0059'),
(10, 'PROD0060'), (7, 'PROD0060');