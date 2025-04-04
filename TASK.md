## Symfony framework, mysql дээрх дата ашиглан department-н өмнөх сарын борлуулалтын орлогыг энэ сартай нь харьцуулсан тайлан гарга. Нэмэлтээр department table CRUD үйлдэлүүдтэй байна. (login болон credentials шаардлагагүй)
# sales
id    month    amount    department_id
1    2025-01    1000    1
2    2025-01    1500    2
3    2025-02    2000    1
4    2025-02    2500    3
5    2025-03    1800    2
6    2025-03    2200    1
7    2025-03    1200    3
8    2025-04    3000    2
9    2025-04    3200    1
10    2025-04    1500    3

# department
id    department_name
1    Sales
2    Marketing
3    IT