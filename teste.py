from selenium import webdriver
from selenium.webdriver.common.by import By
import time

driver = webdriver.Chrome()
driver.get("http://localhost/Marcus/Exercicios/banco/loja.php")
select_produto = driver.find_element(By.ID, "produto")

if(driver.title == "Loja"):
    print("Página carregada com sucesso!")
else:
    print("Falha ao carregar a página.")


time.sleep(5)
driver.quit()