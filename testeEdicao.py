from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
import time

driver = webdriver.Chrome()

driver.get("http://localhost/Marcus/Exercicios/banco/loja.php")

select_element = driver.find_element(By.ID, "produto")
select_produto = Select(select_element)

select_produto.select_by_visible_text("Carne")

time.sleep(2)

botao_editar = driver.find_element(By.ID, "editar")

botao_editar.click()

time.sleep(2)

campo_valor = driver.find_element(By.NAME, "valor")

campo_valor.clear()
campo_valor.send_keys("250.00")

time.sleep(2)

botao_salvar = driver.find_element(By.ID, "atualizar")

botao_salvar.click()

time.sleep(3)

driver.quit()