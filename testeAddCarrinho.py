from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
import time

driver = webdriver.Chrome()
driver.get("http://localhost/Marcus/Exercicios/banco/loja.php")

# Adiciona Chocolate
select_produto = Select(driver.find_element(By.ID, "produto"))
select_produto.select_by_visible_text("Chocolate")
time.sleep(2)

driver.find_element(By.ID, "quantidade").send_keys("2")
time.sleep(2)
driver.find_element(By.ID, "adicionar_carrinho").click()
time.sleep(3)

driver.find_element(By.ID, "continuar_comprando").click()
time.sleep(2)

# Adiciona Carne
select_produto = Select(driver.find_element(By.ID, "produto"))
select_produto.select_by_visible_text("Carne")
time.sleep(2)

driver.find_element(By.ID, "quantidade").send_keys("2")
driver.find_element(By.ID, "adicionar_carrinho").click()
time.sleep(3)

driver.find_element(By.ID, "continuar_comprando").click()
time.sleep(2)

# Adiciona Carne novamente
select_produto = Select(driver.find_element(By.ID, "produto"))
select_produto.select_by_visible_text("Carne")
time.sleep(2)

driver.find_element(By.ID, "quantidade").send_keys("2")
driver.find_element(By.ID, "adicionar_carrinho").click()
time.sleep(3)

driver.quit()