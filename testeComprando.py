from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
import time

driver = webdriver.Chrome()
driver.get("http://localhost/Marcus/Exercicios/banco/loja.php")

# Seleciona Milho
select_produto = Select(driver.find_element(By.ID, "produto"))
select_produto.select_by_visible_text("milho")
time.sleep(2)

# Coloca 20 unidades
campo_quantidade = driver.find_element(By.ID, "quantidade")
campo_quantidade.send_keys("20")
time.sleep(2)

# Adiciona ao carrinho
driver.find_element(By.ID, "adicionar_carrinho").click()
time.sleep(3)

# Limpa o carrinho
driver.find_element(By.ID, "limpar_carrinho").click()
time.sleep(3)

# Volta para a loja
driver.find_element(By.ID, "voltar_loja").click()
time.sleep(3)

# Seleciona Milho novamente
select_produto = Select(driver.find_element(By.ID, "produto"))
select_produto.select_by_visible_text("milho")
time.sleep(2)

# Adiciona agora apenas 15 unidades
campo_quantidade = driver.find_element(By.ID, "quantidade")
campo_quantidade.send_keys("15")
time.sleep(2)

driver.find_element(By.ID, "adicionar_carrinho").click()
time.sleep(3)

# Finaliza a compra
driver.find_element(By.ID, "finalizar_compra").click()
time.sleep(5)

driver.quit()