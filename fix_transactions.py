import os

def fix_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
        
    content = content.replace("->customer_name", "->notes")
    content = content.replace("->total_amount", "->grand_total")
    content = content.replace("->amount_paid", "->amount_paid") # Just in case
    content = content.replace("paid_amount", "amount_paid") # Wait, the model uses amount_paid! Let me verify.

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

views = [
    r'c:\Users\Haikal\Documents\COBA COBA\TOKO\resources\views\transactions\index.blade.php',
    r'c:\Users\Haikal\Documents\COBA COBA\TOKO\resources\views\transactions\show.blade.php',
    r'c:\Users\Haikal\Documents\COBA COBA\TOKO\resources\views\transactions\print.blade.php'
]

for view in views:
    fix_file(view)

print('Done!')
