from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
import time

driver = webdriver.Chrome()
driver.get("http://localhost/Marcus/Exercicios/banco/loja.php")

select_element = driver.find_element(By.ID, "produto")

select_produto = Select(select_element)

select_produto.select_by_visible_text("Chocolate");

time.sleep(5)

driver.quit()