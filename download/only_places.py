with open('loaded.txt', 'r', encoding='utf-8') as fin:
    with open('places.txt', 'w', encoding='utf-8') as fout:
        for line in fin:
            fields = line.strip().split('\t')
            if len(fields) < 3 or fields[2] == 'null':
                continue
            fout.write(line)