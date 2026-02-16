def books_before_10_days(issued_books):
    if(len(issued_books) == 0):
        print("No Book Issued")
    else:  
        print("books issued for more than 10 days:") 
        for issued_book in issued_books:
            if(issued_book[3]>10):
                print(issued_book)
    
def total_books(issued_books):
    return len(issued_books)

issued_books = [
   (101, "Aman", "Python Basics", 7),
   (102, "Riya", "Data Science", 12),
   (103, "Karan", "SQL Guide", 5),
   (104, "Neha", "AI Fundamentals", 15)
]

books_before_10_days(issued_books)
print("Total book issued are ", total_books(issued_books))

# Tuple is suitable for storing each record instead of list because tuple are immutable in nature so it become imposible for someone to manipualte data also tuple takes less space and have less access time than list so reading data from tuple become fast and time efficient.