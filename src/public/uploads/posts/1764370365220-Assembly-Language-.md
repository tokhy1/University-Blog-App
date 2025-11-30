### What is Assembly?

Assembly is a language that makes us interact with the computer (CPU) at a level above the raw of binary language (machine code), It's very low-level programming language that CPU use to execute instructions, so the thing here is the compiler translates code into **assembly (or machine code)** that follows a specific [[Instruction Set Architectures]] (ISA), and that code gets executed by the **CPU** to perform actions. It’s written in a way that’s more readable for humans than the 0s and 1s of machine language.

#### ISA & Assembly

So as we know ISA has two approaches (RISC & CISC), when it comes to assembly the approach of RISC is **Arm Assembly** this is an assembly code that follows this type of architecture, and when it comes to CISC the assembly code will be **x86 Assembly**, both of them will do instructions but in different way or implementation. for example the Arm Assembly is more simple and you'll have to make an instruction for every step, but when it comes to x86 Assembly you can combine two steps into one instruction.

##### Arm VS x86 (Adding two numbers)

**Arm uses a Reduced Instruction Set Computer (RISC) design, meaning instructions are simple and often single-purpose**. 

***Key Difference: x86’s add instruction is "fused"—it combines loading from memory and adding in one step, reducing the instruction count from 4 (in Arm) to 3. This is a perk of CISC architectures.***

##### Core concepts of Assembly Language

Assembly is minimalistic—it lacks the loops, conditionals, and functions of high-level languages. Instead, it’s a sequence of instructions that manipulate data directly. Here are the essentials:
###### 1. **Instructions**

- Each instruction performs a small task, like moving data or doing arithmetic.
- Syntax: mnemonic operand1, operand2, …
- Common examples:
    - mov: Copies data (e.g., mov eax, 1 puts 1 into eax).
    - add: Adds values (e.g., add eax, ebx adds ebx to eax).
    - sub, mul, div: Subtract, multiply, or divide.
###### 2. **Registers**

- Registers are fast, temporary storage inside the CPU.
- In x86, you’ll see:
    - rax, rbx, rcx, rdx, rsi, rdi, r8-r15 (64-bit registers).
    - Smaller versions: eax (32 bits of rax), ax (16 bits), al (8 bits).
- Example: mov rax, rbx copies the value from rbx to rax.
###### 3. **Memory Access**

- Use [] to access memory via a register, like [rsi] (the memory location rsi points to).
- In x86, specify data size with prefixes like DWORD PTR (32 bits).
###### 4. **Operands**

- **Registers**: Like eax or w0.
- **Immediates**: Constant values, like 42 in mov eax, 42.
- **Memory Locations**: Like [rsi] or [rdx].

###### Why Assembly Matters?

- **Closeness to Hardware**: It mirrors machine code, giving you direct control over the CPU.
- **Performance**: You can optimize code beyond what high-level languages allow.
- **Insight**: It reveals how computers process instructions and manage data.


> Assembly language bridges the gap between human-readable code and the binary instructions CPUs execute.


-------------------
