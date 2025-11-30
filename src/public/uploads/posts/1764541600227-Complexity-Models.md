### What is computational complexity?

It's the concept or the way for measuring how much **work** or how **hard** does the computer needs to execute a computer program, it's about measuring the time needed to execute your computer program to decide the rate of optimization you need, this topic says that we can do that by counting the basic operations (addition, multiplication) it needs to do, every operation has a cost in **time** and **work**, so counting the number of operations is helpful, it is helping us in estimating how long a program takes, this idea started in the 1960s when computers were simple, and it worked well back then.

#### How does it connect to computers?

As we know CPU (Central Processing Unit) runs instructions one by one, each instruction takes a certain number in **clock cycles** (a cycle is the measuring unit in CPU), we can play around this if we know the number of operations does one cycle take, we can estimate the time by adding up all the cycles and dividing by the CPU's **clock frequency** (number of cycles per second).

For example if we need to perform multiplications operation with addition, If multiplication takes 3 cycles and addition takes 1 cycle, we can calculate the total cycles:
- Multiplications: 1,000 × 3 = 3,000 cycles
- Additions: 900 × 1 = 900 cycles
- Total: 3,000 + 900 = 3,900 cycles

If the CPU does 1,000 cycles per second, this would take 3.9 seconds. Cool, right? By counting operations and knowing their "cost" in cycles, we can estimate the time!

But we knew these information cause we needed to know:-
- Numbers of cycles does multiplications take (3 cycles in the example).
- Numbers of cycles does additions take (1 cycle in the example).
- Number of operations does multiplications need to perform (1000 in the example).
- Number of operations does additions need to perform (900 in the example).
- Number of cycles per second does the CPU perform (in the example the CPU performs 1000 cycles per second).

**To Wrap up, this simple way of calculating the time rely on the "cost in cycles" (the CPU performs 1,000 in one second).**

##### What is the problem with this way?

Now we cannot rely on clock frequency, because in the modern computers we can't know or even predict the number of cycles does the CPU perform per second, it could be millions or even billions of cycles, but this is the most simple way and in the 1960s it was a good way of measuring and a good way of thinking about calculating programs time. 

**So, to conclude the computational complexity was a simple way, it was a good start to think about measure the time taken to execute the programs, but we can't rely on it and we can't use it anymore.** 

### What’s asymptotic complexity?

This way uses different approach to measure the complexity of our programs, instead of counting the number of operations or number of cycles needed to fit the number of operations, we look at the number of operations grows as the problem size (n) gets bigger, For our example matrix multiplications we don't count for every operation, instead we look for the size range of the problem, if the program gets bigger by $n^2$ so we know that the work getting bigger by n square and so on, this helps us in simplifying things and comparing between algorithms, if one algorithm grow by $n^3$ and another one grow by $n^2$ so the second one is more efficient and much better. 

#### Why we still use it?

It's easy and simple and very useful in big problems, especially when you have to compare between algorithms for solving big problem, cause instead of counting you have to focus and determine the growth rate like ($n^3$), It’s still great for figuring out which algorithm wins when the input is huge.

##### What's the problem with it?

It assumes computers get faster in a simple way (more clock cycles), but that’s not true anymore, Clock speeds have leveled off, and things like memory speed or parallel processing matter more now. Plus, it ignores small details that can make a difference for smaller problems.
###### What does it all mean? 

Computational complexity, especially asymptotic complexity, is a powerful tool to understand and compare algorithms in theory. It tells us how they’ll perform as problems get bigger, which is super helpful. But in the real world, things like hardware quirks and hidden constants also affect speed, so we can’t rely on it alone.

But nowadays, we have a lot of other complexity models to use, there are a lot of ways to measure the time of the algorithm, but choosing the best one depends on the problem you face.


------------------
