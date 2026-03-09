import time
import math

n = 50000000
res = 0.0

start = time.time()

for i in range(1, n + 1):
    res += math.sqrt(i)

finish = time.time()

print(finish - start)