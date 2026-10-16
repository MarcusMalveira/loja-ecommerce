from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
import time

driver = webdriver.Chrome()
driver.get("http://localhost/Marcus/Exercicios/banco/loja.php")

# Seleciona milho
select_produto = Select(driver.find_element(By.ID, "produto"))
select_produto.select_by_visible_text("milho")
time.sleep(1)

# Adiciona 20 unidades
quantidade = driver.find_element(By.ID, "quantidade")
quantidade.send_keys("20")
time.sleep(1)

# Adiciona ao carrinho
driver.find_element(By.ID, "adicionar_carrinho").click()
time.sleep(2)

# Diminui de 20 para 12
for i in range(8):
    driver.find_element(By.ID, "diminuir").click()
    time.sleep(0.5)

# Finaliza a compra
time.sleep(1)
driver.find_element(By.ID, "finalizar_compra").click()
time.sleep(3)

driver.quit()