from selenium import webdriver
from selenium.webdriver.common.by import By
import time

driver = webdriver.Chrome()
driver.get("http://localhost/Marcus/Exercicios/banco/loja.php")
time.sleep(2)

driver.find_element(By.ID, "adicionar").click()
time.sleep(2)

driver.find_element(By.ID, "nome").send_keys("Feijão")
driver.find_element(By.ID, "valor").send_keys("30.00")
driver.find_element(By.ID, "quantidade").send_keys("300")

campo_validade = driver.find_element(By.ID, "validade")
driver.execute_script("arguments[0].value = '2026-08-25';", campo_validade)
time.sleep(2)

driver.find_element(By.ID, "cadastrar").click()
time.sleep(3)

driver.quit()